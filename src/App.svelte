<script lang="ts">
  import Sidebar from './lib/components/Sidebar.svelte'
  import Chat from './lib/components/Chat.svelte'
  import EmptyState from './lib/components/EmptyState.svelte'
  import { getConversations } from './lib/stores/conversations.svelte'

  const store = getConversations()
</script>

<div class="flex h-screen bg-zinc-950 text-white">
  <Sidebar />
  <main class="flex-1 flex flex-col h-full">
    {#if store.activeId}
      <Chat />
    {:else}
      <EmptyState />
    {/if}
  </main>
</div>
