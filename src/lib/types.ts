export interface Conversation {
  id: number
  wa_number: string
  contact_name: string | null
  last_message: string | null
  last_message_at: string | null
  created_at: string
}

export interface Message {
  id: number
  conversation_id: number
  direction: 'inbound' | 'outbound'
  body: string
  twilio_message_sid: string | null
  status: 'queued' | 'sent' | 'delivered' | 'read' | 'failed'
  created_at: string
}

export interface ConversationDetail {
  conversation: Conversation
  messages: Message[]
}
