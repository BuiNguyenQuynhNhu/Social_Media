sequenceDiagram
    autonumber
    actor User as User
    participant UI as Account Settings UI
    participant Ctrl as AuthController
    participant DB as User Database
    participant EmailService as Email Service

    Note over User, DB: USER USE CASE: MANAGE ACCOUNT

    rect rgb(255, 240, 245)
    alt ACTION 1: CHANGE PASSWORD 
        User->>UI: Input Old Pass, New Pass, Confirm Pass
        User->>UI: Click "Update Password"
        UI->>Ctrl: API: changePassword(userId, oldPass, newPass)
        
        Note right of Ctrl: Verify security
        Ctrl->>DB: SELECT password FROM user WHERE id=userId
        DB-->>Ctrl: Return Current Hash
        
        Ctrl->>Ctrl: Verify(oldPass, currentHash)
        
        alt Verification Valid
            Ctrl->>Ctrl: Hash(newPass)
            Ctrl->>DB: UPDATE user SET password = newHash WHERE id=userId
            DB-->>Ctrl: Success
            Ctrl-->>UI: Alert "Password Changed Successfully"
            
            par Security Notification
                Ctrl->>EmailService: Send email "Your password was changed"
            end
        else Verification Failed
            Ctrl-->>UI: Error "Old password incorrect"
        end
    
    else ACTION 2: UPDATE EMAIL/USERNAME 
        User->>UI: Input New Email or Username -> Save
        UI->>Ctrl: API: updateAccountSettings(userId, newEmail, newUsername)
        
        Note right of DB: Check for duplicates
        Ctrl->>DB: SELECT id FROM user WHERE email=newEmail OR username=newUsername
        
        alt No Duplicate Found
            Ctrl->>DB: UPDATE user SET email=newEmail, username=newUsername WHERE id=userId
            DB-->>Ctrl: Success
            Ctrl-->>UI: Alert "Account info updated"
        else Duplicate Exists
            Ctrl-->>UI: Error "Email or Username already taken"
        end
    end
    end