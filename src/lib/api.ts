import type { Conversation, ConversationDetail, Message } from './types'

const isDev = import.meta.env.DEV
const BASE = isDev ? '' : 'https://app.iedeoccidente.com/wpf/public'

async function request<T>(path: string, options?: RequestInit): Promise<T> {
  const res = await fetch(`${BASE}${path}`, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  })
  if (!res.ok) {
    throw new Error(`API error: ${res.status}`)
  }
  return res.json()
}

export function fetchConversations(): Promise<Conversation[]> {
  return request('/api/messages')
}

export function fetchConversation(id: number): Promise<ConversationDetail> {
  return request(`/api/messages/${id}`)
}

export function sendMessage(conversationId: number, body: string): Promise<Message> {
  return request('/api/send', {
    method: 'POST',
    body: JSON.stringify({ conversation_id: conversationId, body }),
  })
}
