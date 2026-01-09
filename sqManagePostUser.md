sequenceDiagram
    autonumber
    actor User
    participant UI as View (Profile / Feed)
    participant Route as CI4 Route
    participant Filter as AuthFilter
    participant Ctrl as PostController
    participant Model as PostModel
    participant DB as MySQL Database

    Note over User, DB: MANAGE USER POSTS (CI4 MVC)

    rect rgb(240,248,255)
    alt CREATE POST
        User->>UI: Enter content
        User->>UI: Click "Post"
        UI->>Route: POST /post/create
        Route->>Filter: Auth Check

        alt Authenticated
            Filter->>Ctrl: Allow Request
            Ctrl->>Model: insert(user_id, content)
            Model->>DB: INSERT INTO posts
            DB-->>Model: Success
            Model-->>Ctrl: postId
            Ctrl-->>UI: Redirect back
            UI-->>User: Display new post
        else Not Authenticated
            Filter-->>UI: Redirect to /login
        end

    else EDIT POST
        User->>UI: Edit Post
        UI->>Route: POST /post/update/{id}
        Route->>Filter: Auth Check
        Filter->>Ctrl: Allow

        Ctrl->>Model: checkOwner(postId, userId)
        Model->>DB: SELECT post
        DB-->>Model: Result

        alt Is Owner
            Ctrl->>Model: update(postId)
            Model->>DB: UPDATE posts
            DB-->>Ctrl: Success
            Ctrl-->>UI: Update View
        else Not Owner
            Ctrl-->>UI: Error Unauthorized
        end

    else DELETE POST
        User->>UI: Click Delete
        UI->>Route: POST /post/delete/{id}
        Route->>Filter: Auth Check
        Filter->>Ctrl: Allow

        Ctrl->>Model: delete(postId)
        Model->>DB: DELETE posts
        DB-->>Ctrl: Success
        Ctrl-->>UI: Remove post
    end
    end
