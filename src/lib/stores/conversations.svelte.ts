import { fetchConversations, fetchConversation, sendMessage as apiSend } from '$lib/api'
import type { Conversation, Message } from '$lib/types'

let conversations = $state<Conversation[]>([])
let activeId = $state<number | null>(null)
let messages = $state<Message[]>([])
let loading = $state(false)
let unread = $state<Record<number, number>>({})
let pollTimer: ReturnType<typeof setInterval> | null = null
let chatTimer: ReturnType<typeof setInterval> | null = null
let lastMessageCount = 0

export function getConversations() {
  return {
    get list() { return conversations },
    get activeId() { return activeId },
    get messages() { return messages },
    get loading() { return loading },
    get unread() { return unread },
  }
}

function notifyNewMessage(conv: Conversation) {
  if (Notification.permission !== 'granted') return
  if (document.hasFocus()) return
  new Notification('wpFatima', {
    body: conv.last_message || 'Nuevo mensaje',
    icon: '/wpf/favicon.svg',
  })
}

export async function loadConversations() {
  const prev = conversations.map(c => `${c.id}:${c.last_message}`)
  conversations = await fetchConversations()

  if (prev.length > 0) {
    for (const conv of conversations) {
      const was = prev.find(p => p.startsWith(`${conv.id}:`))
      if (was && was.split(':').slice(1).join(':') !== (conv.last_message || '')) {
        if (conv.id !== activeId) {
          unread[conv.id] = (unread[conv.id] || 0) + 1
          notifyNewMessage(conv)
        }
      }
    }
  }
}

export async function selectConversation(id: number) {
  activeId = id
  unread[id] = 0
  loading = true
  try {
    const detail = await fetchConversation(id)
    messages = detail.messages
    lastMessageCount = messages.length
  } finally {
    loading = false
  }
}

async function refreshActiveChat() {
  if (!activeId) return
  try {
    const detail = await fetchConversation(activeId)
    if (detail.messages.length > lastMessageCount) {
      messages = detail.messages
      lastMessageCount = messages.length
    }
  } catch {}
}

export async function send(body: string): Promise<any> {
  if (!activeId || !body.trim()) return null
  const msg = await apiSend(activeId, body.trim())
  messages = [...messages, msg]
  lastMessageCount = messages.length

  const idx = conversations.findIndex(c => c.id === activeId)
  if (idx !== -1) {
    conversations[idx].last_message = body.trim()
    conversations[idx].last_message_at = msg.created_at
    conversations = [...conversations]
  }
  return msg
}

export function startPolling() {
  if (pollTimer) return
  pollTimer = setInterval(loadConversations, 5000)
  chatTimer = setInterval(refreshActiveChat, 3000)
}

export function stopPolling() {
  if (pollTimer) { clearInterval(pollTimer); pollTimer = null }
  if (chatTimer) { clearInterval(chatTimer); chatTimer = null }
}

export function clearActive() {
  activeId = null
  messages = []
  lastMessageCount = 0
}
