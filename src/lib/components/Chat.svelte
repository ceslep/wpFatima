<script lang="ts">
  import { getConversations } from '$lib/stores/conversations.svelte'
  import MessageBubble from './MessageBubble.svelte'
  import ComposeBar from './ComposeBar.svelte'

  const store = getConversations()
  let chatEnd = $state<HTMLDivElement>()

  $effect(() => {
    store.messages
    chatEnd?.scrollIntoView({ behavior: 'smooth' })
  })

  let activeConv = $derived(store.list.find(c => c.id === store.activeId))
</script>

<div class="flex flex-col h-full bg-zinc-950">
  {#if activeConv}
    <div class="border-b border-zinc-700 bg-zinc-900 px-4 py-3">
      <h2 class="text-sm font-semibold text-white">
        {activeConv.contact_name || activeConv.wa_number}
      </h2>
      <p class="text-[10px] text-zinc-500">{activeConv.wa_number}</p>
    </div>
  {/if}

  <div class="flex-1 overflow-y-auto px-4 py-3">
    {#if store.loading}
      <div class="flex justify-center py-8">
        <span class="text-zinc-500 text-sm">Cargando...</span>
      </div>
    {:else if store.messages.length === 0}
      <div class="flex justify-center py-8">
        <span class="text-zinc-600 text-sm">No hay mensajes</span>
      </div>
    {:else}
      {#each store.messages as msg (msg.id)}
        <MessageBubble message={msg} />
      {/each}
    {/if}
    <div bind:this={chatEnd}></div>
  </div>

  <ComposeBar />
</div>
