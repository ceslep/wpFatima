<script lang="ts">
  import type { Message } from '$lib/types'

  let { message }: { message: Message } = $props()
  let isOutbound = $derived(message.direction === 'outbound')
  let time = $derived(new Date(message.created_at).toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' }))
</script>

<div class="flex {isOutbound ? 'justify-end' : 'justify-start'} mb-2">
  <div class="max-w-[75%] rounded-2xl px-4 py-2 {isOutbound ? 'bg-emerald-600 text-white rounded-br-md' : 'bg-zinc-700 text-zinc-100 rounded-bl-md'}">
    <p class="text-sm whitespace-pre-wrap break-words">{message.body}</p>
    <div class="flex items-center justify-end gap-1 mt-1">
      <span class="text-[10px] {isOutbound ? 'text-emerald-200' : 'text-zinc-400'}">{time}</span>
      {#if isOutbound}
        <span class="text-[10px] text-emerald-200">
          {#if message.status === 'read'}✓✓{:else if message.status === 'delivered'}✓✓{:else if message.status === 'sent'}✓{:else if message.status === 'failed'}✗{:else}○{/if}
        </span>
      {/if}
    </div>
  </div>
</div>
