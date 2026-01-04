sequenceDiagram
    autonumber
    actor Member as Group Member
    participant UI as Group Page UI
    participant Ctrl as GroupController
    participant DB as Group/Post Database
    participant Noti as Notification Service

    Note over Member, DB: USER USE CASE: INTERACT WITH GROUP

    rect rgb(240, 248, 255)
    alt ACTION 1: CREATE POST IN GROUP 
        Member->>UI: Write Content, Attach Media -> Click "Post"
        UI->>Ctrl: API: createGroupPost(userId, groupId, content, media)
        
        Note right of DB: Insert into 'post' linked to 'group'
        Ctrl->>DB: INSERT INTO post (content, mediaURL, privacy='group', groupId=...)
        DB-->>Ctrl: Success (Returns newPostId)
        
        alt Auto-Approve
            Ctrl->>DB: UPDATE post SET status='active' WHERE id=newPostId
            Ctrl->>Noti: Notify other members
            Ctrl-->>UI: Display Post on Group Feed
        else Admin Approval Required
            Ctrl->>DB: UPDATE post SET status='pending' WHERE id=newPostId
            Ctrl-->>UI: Alert "Post submitted for review"
        end

    else ACTION 2: VIEW MEMBERS 
        Member->>UI: Click "Members" Tab
        UI->>Ctrl: API: getGroupMembers(groupId)
        
        Note right of DB: Join 'group_user', 'user', 'profile'
        Ctrl->>DB: SELECT u.avatar, p.first_name, p.last_name FROM group_user gu JOIN user u...
        DB-->>Ctrl: Return List
        Ctrl-->>UI: Display Member Grid
        
    else ACTION 3: LEAVE GROUP 
        Member->>UI: Click "Joined" -> Select "Leave Group"
        UI->>Member: Confirm Popup "Are you sure?"
        Member->>UI: Click "Yes"
        
        UI->>Ctrl: API: leaveGroup(userId, groupId)
        
        Note right of DB: Remove record from 'group_user'
        Ctrl->>DB: DELETE FROM group_user WHERE userId=... AND groupId=...
        DB-->>Ctrl: Success
        
        Ctrl->>DB: UPDATE group SET member_count = member_count - 1
        Ctrl-->>UI: Redirect to Home Page
    end
    end