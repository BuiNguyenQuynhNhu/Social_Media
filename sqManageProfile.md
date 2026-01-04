sequenceDiagram
    autonumber
    actor User as User
    participant UI as Profile UI
    participant Ctrl as UserController
    participant DB as User/Profile Database

    Note over User, DB: USER USE CASE: MANAGE PROFILE

    rect rgb(240, 255, 240)
    alt ACTION 1: VIEW PROFILE
        User->>UI: Access Profile Page
        UI->>Ctrl: API: getProfile(userId)
        
        Note right of DB: Join table 'user' and 'profile'
        Ctrl->>DB: SELECT u.avatar, p.* FROM user u JOIN profile p ON u.id = p.id...
        DB-->>Ctrl: Return Profile Data
        Ctrl-->>UI: Render Profile Page (Avatar, Name, Bio...)
    end

    alt ACTION 2: UPDATE INFO 
        User->>UI: Click "Edit Profile"
        User->>UI: Change Name, Gender, Birthday -> Click "Save"
        UI->>Ctrl: API: updateProfileInfo(userId, firstName, lastName, gender, birth)
        
        Ctrl->>DB: UPDATE profile SET first_name=..., last_name=..., gender=... WHERE id=...
        DB-->>Ctrl: Success
        Ctrl-->>UI: Show success message "Info Updated"
    end

    alt ACTION 3: UPDATE AVATAR/BACKGROUND
        User->>UI: Upload new Avatar/Background Image
        UI->>Ctrl: API: updateImages(userId, imageURL, type)
        
        alt Type is Avatar
            Note right of DB: 'avatar' is in 'user' table
            Ctrl->>DB: UPDATE user SET avatar = imageURL WHERE id=...
        else Type is Background
            Note right of DB: 'background' is in 'profile' table
            Ctrl->>DB: UPDATE profile SET background = imageURL WHERE id=...
        end
        
        DB-->>Ctrl: Success
        Ctrl-->>UI: Refresh Image on Screen
    end
    end