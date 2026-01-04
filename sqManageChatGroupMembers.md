sequenceDiagram
    autonumber
    actor Admin as Chat Admin
    participant UI as Group Info UI
    participant Ctrl as ChatGroupController
    participant DB as Chat Database
    participant Noti as Notification Service
    actor Target as Target User

    Note over Admin, DB: USE CASE: MANAGE CHAT MEMBERS

    rect rgb(240, 248, 255)
    alt ACTION 1: ADD MEMBER 
        Admin->>UI: Search User by Name/Phone -> Click "Add"
        UI->>Ctrl: API: addChatMember(groupId, targetId)
        
        Ctrl->>DB: Check if user exists in group
        
        alt User Not in Group
            Note right of DB: Insert into 'group_chat_user'
            Ctrl->>DB: INSERT INTO group_chat_user (nickname, joinAt, userId, groupId)
            DB-->>Ctrl: Success
            
            Ctrl->>Noti: Notify Target User
            Noti-->>Target: "You have been added to the chat group"
            
            Ctrl-->>UI: Update Member List
        else User Already Exists
            Ctrl-->>UI: Error "User is already in the group"
        end

    else ACTION 2: KICK/REMOVE MEMBER 
        Admin->>UI: Select Member -> Click "Remove from Group"
        UI->>Ctrl: API: removeChatMember(groupId, targetId)
        
        Note right of DB: Delete from 'group_chat_user'
        Ctrl->>DB: DELETE FROM group_chat_user WHERE userId=... AND groupId=...
        DB-->>Ctrl: Success
        
        par System Actions
            Ctrl->>Noti: Notify Target User "You were removed"
            Ctrl->>DB: Log action to System Chat (e.g., "Admin removed User")
        end
        
        Ctrl-->>UI: Remove User from List
    end
    end