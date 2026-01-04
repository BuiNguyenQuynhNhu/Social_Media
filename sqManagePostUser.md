sequenceDiagram
    autonumber
    actor User as User
    participant UI as Post Creation/Edit UI
    participant Ctrl as PostController
    participant DB as Post Database

    Note over User, DB: MANAGE OWN POSTS

    rect rgb(240, 248, 255)
    alt ACTION 1: CREATE NEW POST
        User->>UI: Input Content, Upload Image, Select Privacy
        User->>UI: Click "Post" Button
        UI->>Ctrl: API: createPost(userId, content, mediaURL, privacy)
        
        Note right of Ctrl: Class attributes used: content, mediaURL, privacy
        Ctrl->>DB: INSERT INTO post (content, mediaURL, privacy, creatAt)
        DB-->>Ctrl: Success (Returns new postId)
        
        Ctrl-->>UI: Return Success Data
        UI-->>User: Display new post on Feed
    
    else ACTION 2: EDIT POST
        User->>UI: Click "Edit" -> Modify Content/Privacy -> Save
        UI->>Ctrl: API: updatePost(postId, newContent, newPrivacy)
        
        Ctrl->>DB: Check Post Ownership (SELECT * FROM post WHERE id=... AND userId=...)
        
        alt User is Owner
            Ctrl->>DB: UPDATE post SET content=..., privacy=... WHERE id=...
            DB-->>Ctrl: Success
            Ctrl-->>UI: Update Post Display
            UI-->>User: Notification "Post Updated"
        else Not Owner
            Ctrl-->>UI: Error "Unauthorized"
        end

    else ACTION 3: DELETE POST
        User->>UI: Click "Delete" -> Confirm Popup
        UI->>Ctrl: API: deletePost(postId)
        
        Ctrl->>DB: DELETE FROM post WHERE id=postId
        DB-->>Ctrl: Success
        
        Ctrl-->>UI: Remove Post from View
        UI-->>User: Notification "Post Deleted"
    end
    end