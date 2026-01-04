sequenceDiagram
    autonumber
    actor Owner as Group Owner/Main Admin
    participant UI as Admin Management UI
    participant Ctrl as ChatGroupController
    participant DB as Chat Database
    participant Noti as Notification Service
    actor Member as Target Member

    Note over Owner, DB: USE CASE: MANAGE ADMINS

    rect rgb(255, 250, 240)
    alt ACTION 1: PROMOTE TO ADMIN 
        Owner->>UI: Select Member -> Click "Appoint as Admin"
        UI->>Ctrl: API: promoteToAdmin(groupId, memberId)
        
        Note right of DB: Insert into 'admin_chat' table
        Ctrl->>DB: INSERT INTO admin_chat (role='admin', userId, groupId)
        DB-->>Ctrl: Success
        
        par Notify Member
            Ctrl->>Noti: Send Notification
            Noti-->>Member: "You are now an Admin of this group"
        end
        
        Ctrl-->>UI: Update Member Role Badge (Show 'Admin')

    else ACTION 2: DISMISS/DEMOTE ADMIN 
        Owner->>UI: Select Admin -> Click "Dismiss as Admin"
        UI->>Ctrl: API: dismissAdmin(groupId, adminId)
        
        Note right of DB: Delete from 'admin_chat'
        Ctrl->>DB: DELETE FROM admin_chat WHERE userId=... AND groupId=...
        DB-->>Ctrl: Success
        
        Note right of Ctrl: User remains in 'group_chat_user'
        Ctrl->>Noti: Notify User "Your admin rights were revoked"
        Ctrl-->>UI: Revert Role Badge to 'Member'
    end
    end