sequenceDiagram
    autonumber
    actor User as User (Interactor)
    participant UI as Newsfeed/Post UI
    participant Ctrl as PostController
    participant DB as Interact/Comment Database
    participant Noti as Notification Service
    actor Owner as Post Owner

    Note over User, DB: USER USE CASE: INTERACT WITH POST

    rect rgb(255, 250, 240)
    alt ACTION 1: REACT TO POST
        User->>UI: Click "Like" Icon
        UI->>Ctrl: API: toggleInteract(userId, postId, type='LIKE')
        
        Note right of DB: Check 'interact' table
        Ctrl->>DB: SELECT * FROM interact WHERE userId=... AND postId=...
        
        alt Interaction does not exist (Add Like)
            Ctrl->>DB: INSERT INTO interact (userId, postId, type, creatAt)
            DB-->>Ctrl: Success
            
            par Notify Owner
                Ctrl->>Noti: Create Notification
                Noti-->>Owner: "User liked your post"
            end
            
            Ctrl-->>UI: Return status: Liked (Active Icon)
        else Interaction exists (Unlike)
            Ctrl->>DB: DELETE FROM interact WHERE userId=... AND postId=...
            DB-->>Ctrl: Success
            Ctrl-->>UI: Return status: Unliked (Inactive Icon)
        end

    else ACTION 2: COMMENT ON POST
        User->>UI: Write content -> Press Enter
        UI->>Ctrl: API: addComment(userId, postId, content, image)
        
        Note right of DB: Insert into 'comment' table
        Ctrl->>DB: INSERT INTO comment (content, image, creatAt, userId, postId)
        DB-->>Ctrl: Success (Returns commentId)
        
        par Notify Owner
            Ctrl->>Noti: Create Notification
            Noti-->>Owner: "User commented on your post"
        end
        
        Ctrl-->>UI: Append new comment to the list
        UI-->>User: View updated comment section
    end
    end