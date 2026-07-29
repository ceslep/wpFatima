<script lang="ts">
  import { send } from '$lib/stores/conversations.svelte'

  let text = $state('')
  let sending = $state(false)
  let error = $state('')

  async function handleSend() {
    if (!text.trim() || sending) return
    sending = true
    error = ''
    try {
      const result = await send(text)
      if (result?.twilio_error) {
        error = result.twilio_error
      } else {
        text = ''
      }
    } catch (e: any) {
      error = e.message || 'Error al enviar'
    } finally {
      sending = false
    }
  }

  function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      handleSend()
    }
  }
</script>

<div class="border-t border-zinc-700 bg-zinc-800 p-3">
  {#if error}
    <div class="mb-2 px-3 py-2 rounded-lg bg-red-900/50 border border-red-700 text-xs text-red-300">
      {error}
    </div>
  {/if}
  <div class="flex items-end gap-2">
    <textarea
      bind:value={text}
      onkeydown={handleKeydown}
      placeholder="Escribe un mensaje..."
      rows="1"
      class="flex-1 resize-none rounded-xl bg-zinc-700 px-4 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 outline-none focus:ring-2 focus:ring-emerald-500"
    ></textarea>
    <button
      onclick={handleSend}
      disabled={!text.trim() || sending}
      class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed"
    >
      {sending ? '...' : 'Enviar'}
    </button>
  </div>
</div>
