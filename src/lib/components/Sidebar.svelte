<script lang="ts">
  import { getConversations, selectConversation, loadConversations, startPolling } from '$lib/stores/conversations.svelte'

  const store = getConversations()

  $effect(() => {
    loadConversations()
    startPolling()
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission()
    }
  })

  function formatTime(dateStr: string | null): string {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    const now = new Date()
    if (d.toDateString() === now.toDateString()) {
      return d.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' })
    }
    return d.toLocaleDateString('es', { day: '2-digit', month: '2-digit' })
  }
</script>

<aside class="w-80 bg-zinc-900 border-r border-zinc-700 flex flex-col h-full">
  <div class="p-4 border-b border-zinc-700">
    <h1 class="text-lg font-semibold text-white">wpFatima</h1>
    <p class="text-xs text-zinc-500">WhatsApp Messenger</p>
  </div>

  <div class="flex-1 overflow-y-auto">
    {#if store.list.length === 0}
      <p class="p-4 text-sm text-zinc-500 text-center">No hay conversaciones</p>
    {/if}

    {#each store.list as conv (conv.id)}
      {@const count = store.unread[conv.id] || 0}
      <button
        class="w-full text-left px-4 py-3 border-b border-zinc-800 transition hover:bg-zinc-800 {store.activeId === conv.id ? 'bg-zinc-800' : ''}"
        onclick={() => selectConversation(conv.id)}
      >
        <div class="flex items-center justify-between mb-1">
          <span class="text-sm font-medium text-white truncate flex items-center gap-2">
            {conv.contact_name || conv.wa_number}
            {#if count > 0}
              <span class="bg-emerald-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                {count}
              </span>
            {/if}
          </span>
          <span class="text-[10px] text-zinc-500 shrink-0 ml-2">
            {formatTime(conv.last_message_at)}
          </span>
        </div>
        <p class="text-xs text-zinc-400 truncate">{conv.last_message || 'Sin mensajes'}</p>
      </button>
    {/each}
  </div>
</aside>
