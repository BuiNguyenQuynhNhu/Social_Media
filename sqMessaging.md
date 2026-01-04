sequenceDiagram
    autonumber
    actor Sender as User A (Sender)
    participant UI as Chat Window UI
    participant Ctrl as MessageController
    participant DB as Message Database
    participant Socket as RealTime Service
    actor Receiver as User B (Receiver)

    Note over Sender, DB: SCENARIO 1: OPEN CHAT & LOAD HISTORY
    Sender->>UI: Click on User B to Chat
    UI->>Ctrl: API: getMessages(chatId, limit=20)
    
    Ctrl->>DB: SELECT * FROM message WHERE chatId=... ORDER BY createAt DESC
    DB-->>Ctrl: Return List of Messages
    Ctrl-->>UI: Display Conversation History
    
    Note over Sender, Receiver: SCENARIO 2: SENDING A MESSAGE
    Sender->>UI: Type "Hello!" -> Press Send
    UI->>Ctrl: API: sendMessage(senderId, chatId, content)
    
    par Save & Deliver
        Note right of DB: Save to DB for persistence
        Ctrl->>DB: INSERT INTO message (content, createAt, saw=false, senderId...)
        DB-->>Ctrl: Success (Returns messageId)
        
        Note right of Socket: Real-time delivery via WebSocket/Socket.io
        Ctrl->>Socket: Emit 'new_message' event {content, senderId...}
    end

    Socket-->>Receiver: Push Message (Pop-up/Chat Bubble)
    Ctrl-->>UI: Append message to Chat Log (Status: Sent)

    Note over Receiver, DB: SCENARIO 3: RECEIVE & MARK AS READ
    opt Receiver is online and opens Chat
        Receiver->>UI: View the Message
        UI->>Ctrl: API: markAsRead(messageId)
        
        Note right of DB: Update 'saw' attribute from Class Diagram
        Ctrl->>DB: UPDATE message SET saw = TRUE WHERE id = messageId
        DB-->>Ctrl: Success
        
        Ctrl->>Socket: Emit 'message_read' event to Sender
        Socket-->>Sender: Update status to "Seen" (Icon changes)
    end