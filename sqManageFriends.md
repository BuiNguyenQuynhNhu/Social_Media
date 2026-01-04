sequenceDiagram
    autonumber
    actor User as User (Sender/Actor)
    participant UI as Friends List/Profile UI
    participant Ctrl as FriendController
    participant DB as Friendship Database
    participant Noti as Notification Service
    actor Target as Target User (Receiver)

    Note over User, DB: USER USE CASE: MANAGE FRIENDS

    rect rgb(255, 250, 240)
    alt ACTION 1: SEND FRIEND REQUEST
        User->>UI: Click "Add Friend" on Target Profile
        UI->>Ctrl: API: sendFriendRequest(userId, targetId)
        
        Ctrl->>DB: Check if relationship exists (SELECT * FROM friendship...)
        
        alt No existing relationship
            Ctrl->>DB: INSERT INTO friendship (id1, id2, status='pending')
            DB-->>Ctrl: Success
            
            Ctrl->>Noti: Create Notification
            Noti-->>Target: "User sent you a friend request"
            Ctrl-->>UI: Button changes to "Request Sent"
        else Already exists
            Ctrl-->>UI: Error "Request already sent or already friends"
        end

    else ACTION 2: RESPOND TO REQUEST
        Target->>UI: View Friend Request List
        
        opt Accept Request
            Target->>UI: Click "Confirm/Accept"
            UI->>Ctrl: API: respondFriendRequest(requestId, action='accept')
            Ctrl->>DB: UPDATE friendship SET status='accepted' WHERE id...
            DB-->>Ctrl: Success
            
            par Notify Sender
                Ctrl->>Noti: Notify User
                Noti-->>User: "Target User accepted your friend request"
            end
            Ctrl-->>UI: Update List (Move to Friends)
        end

        opt Reject Request
            Target->>UI: Click "Delete/Reject"
            UI->>Ctrl: API: respondFriendRequest(requestId, action='reject')
            Ctrl->>DB: DELETE FROM friendship WHERE id...
            DB-->>Ctrl: Success (Row deleted)
            Ctrl-->>UI: Remove from Request List
        end

    else ACTION 3: UNFRIEND
        User->>UI: Visit Friend's Profile -> Click "Unfriend"
        UI->>User: Show Confirmation Popup
        User->>UI: Confirm "Yes"
        
        UI->>Ctrl: API: unfriend(userId, friendId)
        
        Note right of DB: Removes record linking id1 and id2
        Ctrl->>DB: DELETE FROM friendship WHERE (id1=... AND id2=...)
        DB-->>Ctrl: Success
        
        Ctrl-->>UI: Update Button to "Add Friend"
        UI-->>User: Notification "Unfriended successfully"
    end
    end