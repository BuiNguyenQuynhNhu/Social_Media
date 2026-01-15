<?php 
    $isOwnProfile = (session()->get('user_id') == $user['id']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($user['firstname'] . ' ' . $user['lastname']) ?> | Profile</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
<<<<<<< HEAD

    <script>
        const APP_CONFIG = {
            baseURL: '<?= base_url() ?>',
            userId: '<?= session()->get('user_id') ?>', 
            targetId: '<?= $profileId ?? session()->get("user_id") ?>',
            myAvatar: '<?= $myAvatar ?>',
            csrfTokenName: '<?= csrf_token() ?>',     
            csrfHash: '<?= csrf_hash() ?>'
        };
    </script>

    <script src="<?= base_url('JS/post.js') ?>"></script>
=======
>>>>>>> admin
    <link rel="stylesheet" href="<?= base_url('CSS/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/User/profile.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/overall.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/post.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/comment.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/post/interaction.css') ?>">
    <link rel="stylesheet" href="<?= base_url('CSS/dashboard.css') ?>">
</head>

<body class="background">
<<<<<<< HEAD

    <?= view('templates/UserNavbar', ['avatarUrl' => $myAvatar]) ?>

    <div class="profile-container">
        
        <div class="fb-card profile-header">

            <!-- COVER -->
            <?php
                $bgPath = $user['background'] ?? 'ICON/default-cover.jpg';

                $bgFinal = str_starts_with($bgPath, 'http') ? $bgPath : base_url($bgPath);
            ?>
            <div class="cover-photo" style="background-image: url('<?= $bgFinal ?>'); position: relative;">

            </div>

            <!-- AVATAR + NAME -->
            <div class="profile-identity">
                <div class="avatar-wrapper">
                    <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>"
                        class="profile-avatar">
                </div>

                <div class="profile-text">
                    <div class="fullname">
                        <?= esc($user['firstname'] . ' ' . $user['lastname']) ?>
                    </div>
                    <div class="friends-count">
                        <?= esc($friendsCount ?? 0) ?> friends
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="profile-actions">
                    <?php if ($friendStatus === 'self'): ?>
                        <button class="btn btn-secondary"
                                onclick="document.getElementById('edit-modal').style.display='flex'">
                            <i data-lucide="edit"></i> Edit profile
                        </button>

                    <?php elseif ($friendStatus === 'friends'): ?>
                        <button class="btn btn-secondary"
                                onclick="unfriendUser('<?= $profileId ?>')">
                            <i data-lucide="user-check"></i> Friends
                        </button>

                    <?php elseif ($friendStatus === 'request_sent'): ?>
                        <button class="btn btn-secondary"
                                onclick="cancelRequest('<?= $profileId ?>')">
                            <i data-lucide="user-minus"></i> Cancel request
                        </button>

                    <?php elseif ($friendStatus === 'request_received'): ?>
                        <button class="btn btn-primary"
                                onclick="acceptFriend('<?= $profileId ?>')">
                            Confirm
                        </button>
                    
                    <?php else: ?>
                        <button class="btn btn-primary"
                                onclick="addFriend('<?= $profileId ?>')">
                            <i data-lucide="user-plus"></i> Add friend
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-divider"></div>
        </div>


        <div class="profile-body">
            
            <div class="left-column">
             
                <?php if(session()->getFlashdata('error')): ?>
                    <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 10px;">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('success')): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 10px;">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="fb-card intro-section">
                    <h3>Intro</h3>

                    <!-- FULL NAME -->
                    <div class="intro-item">
                        <i data-lucide="id-card"></i>
                        <span>
                            <?= esc($user['firstname'] . ' ' . $user['lastname']) ?>
                        </span>
                    </div>

                    <!-- GENDER -->
                    <div class="intro-item">
                        <i data-lucide="user"></i>
                        <span>
                            Gender:
                            <strong><?= esc($user['gender'] ?? 'Not set') ?></strong>
                        </span>
                    </div>

                    <!-- BIRTHDAY -->
                    <div class="intro-item">
                        <i data-lucide="cake"></i>
                        <span>
                            Birthday:
                            <strong>
                                <?= !empty($user['birth'])
                                    ? date('d F Y', strtotime($user['birth']))
                                    : 'Not set' ?>
                            </strong>
                        </span>
                    </div>

                    <!-- JOINED DATE -->
                    <?php if (!empty($user['created_at'])): ?>
                        <div class="intro-item">
                            <i data-lucide="calendar"></i>
                            <span>
                                Joined
                                <strong><?= date('F Y', strtotime($user['created_at'])) ?></strong>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- ACTIONS -->
                    <div id="friend-action-container" style="margin-top: 20px;">
                        <?php if ($friendStatus === 'self'): ?>
                            <button class="btn btn-full-gray"
                                    onclick="document.getElementById('edit-modal').style.display='flex'">
                                <i data-lucide="edit"></i> Edit profile
                            </button>

                        <?php elseif ($friendStatus === 'friends'): ?>
                            <button class="btn btn-full-gray"
                                    onclick="unfriendUser('<?= $profileId ?>')">
                                <i data-lucide="user-check"></i> Friends
                            </button>

                        <?php elseif ($friendStatus === 'request_sent'): ?>
                            <button class="btn btn-full-gray"
                                    onclick="cancelRequest('<?= $profileId ?>')">
                                <i data-lucide="user-minus"></i> Cancel request
                            </button>

                        <?php elseif ($friendStatus === 'request_received'): ?>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-primary"
                                        style="flex:1"
                                        onclick="acceptFriend('<?= $profileId ?>')">
                                    Confirm
                                </button>
                                <button class="btn btn-full-gray"
                                        style="flex:1"
                                        onclick="cancelRequest('<?= $profileId ?>')">
                                    Delete
                                </button>
                            </div>

                        <?php else: ?>
                            <button class="btn btn-primary"
                                    style="width:100%"
                                    onclick="addFriend('<?= $profileId ?>')">
                                <i data-lucide="user-plus"></i> Add friend
                            </button>
                        <?php endif; ?>
                    </div>
=======
    <?= view('components/user/navbar', ['avatarUrl' => $avatarUrl]) ?>
    <div class="profile-container">
        <?= view('components/user/header') ?>

        <div class="profile-body-layout">
            <div class="left-column">
                <div class="fb-card intro-section">
                    <h3>Intro</h3>
                    <div class="intro-item"><i data-lucide="user"></i><span>Gender: <strong><?= esc($user['gender'] ?? 'Not set') ?></strong></span></div>
                    <div class="intro-item"><i data-lucide="cake"></i><span>Birthday: <strong><?= !empty($user['birth']) ? date('d F Y', strtotime($user['birth'])) : 'Not set' ?></strong></span></div>
>>>>>>> admin
                </div>
            </div>

            <div class="right-column">
<<<<<<< HEAD
                <?php if ($friendStatus === 'self'): ?>
                    <div class="fb-card create-post">
                        <?php if (session()->get('errors')) : ?>
                            <div style="color: red; font-size: 13px; margin-bottom: 10px; padding: 0 10px;">
                                <?php foreach (session()->get('errors') as $error) : ?>
                                    <p>• <?= esc($error) ?></p>
                                <?php endforeach ?>
                            </div>
                        <?php endif; ?>

                        <div class="create-post-trigger" onclick="openPostModal()" style="display: flex; align-items: center; cursor: pointer; padding: 10px;">
                            <div class="trigger-avatar" style="margin-right: 10px;">
                                <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" alt="User" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </div>
                            <div class="trigger-input" style="background: #f0f2f5; padding: 10px 15px; border-radius: 20px; flex-grow: 1; color: #65676b;">
                                What's on your mind, <?= esc($user['firstname'] ?? 'User') ?>?
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="feed-container">
                </div>

                <div id="pagination-controls" class="pagination-bar">
                    </div>
            </div>
        </div>
    </div>
    <div id="postModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            
            <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #ddd;">
                <h3 style="flex-grow: 1; text-align: center; margin: 0; font-size: 20px; font-weight: 700;">Create Post</h3>
                <div onclick="closePostModal()" style="cursor: pointer; width: 36px; height: 36px; background: #e4e6eb; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="x"></i>
                </div>
            </div>

            <form id="createPostForm" onsubmit="submitCreatePost(event)" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 15px;">
                    <div class="user-profile-row" style="display: flex; align-items: center; margin-bottom: 15px;">
                        <img src="<?= $avatarUrl ?? base_url('ICON/avatar-default.svg') ?>" alt="User" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <div>
                            <div style="font-weight: 600; font-size: 15px;">
                                <?= esc($user['firstname'] . ' ' . $user['lastname']) ?>
                            </div>
                            <select name="privacy" style="background: #e4e6eb; border: none; border-radius: 6px; padding: 4px 8px; font-size: 13px; font-weight: 600; margin-top: 4px; cursor: pointer;">
                                <option value="public">🌎 Public</option>
                                <option value="friends">👥 Friends</option>
                                <option value="private">🔒 Only me</option>
                            </select>
                        </div>
                    </div>

                    <textarea name="content" 
                            placeholder="What's on your mind, <?= esc($user['firstname']) ?>?" 
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 24px; min-height: 120px; font-family: inherit; margin-bottom: 10px;"></textarea>

                    <div id="image-preview-container" style="display:none; margin-top:10px; position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 5px;">
                        <img id="image-preview" src="#" alt="Image Preview" style="width: 100%; max-height: 300px; object-fit: contain; border-radius: 4px;">
                        <button type="button" onclick="removeImage()" style="position: absolute; top: 10px; right: 10px; background: white; border: 1px solid #ccc; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 15px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <div class="upload-btn-wrapper">
                        <label for="post-image" style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 5px; background-color: #f0f2f5;" title="Photo/Video">
                            <span style="font-size: 24px;">📷</span> 
                        </label>
                        <input type="file" name="image" id="post-image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 30px; font-weight: bold; background-color: #1877f2; color: white; border: none; border-radius: 6px; font-size: 15px; cursor: pointer;">Post</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button onclick="document.getElementById('edit-modal').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
            </div>

            <div class="modal-body">
                
                <form action="<?= base_url('user/update_profile') ?>" method="post" class="edit-form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1c1e21;">Cover Photo</label>
                        <input type="file" name="background" accept="image/*" class="form-control" 
                               style="border: 1px solid #ddd; padding: 8px; width: 100%; border-radius: 6px; background: #f0f2f5;">
                    </div>

                    <div style="text-align: center; margin-bottom: 15px;">
                        <img id="edit-avatar-preview" src="<?= $avatarUrl ?>" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; margin-bottom: 10px;">
                        <br>
                        <label for="avatar_upload" style="cursor: pointer; color: #1877f2; font-weight: 600;">Change Avatar</label>
                        <input type="file" id="avatar_upload" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex: 1;">
                            <label>First Name</label>
                            <input type="text" name="firstname" value="<?= esc($user['firstname']) ?>" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Last Name</label>
                            <input type="text" name="lastname" value="<?= esc($user['lastname']) ?>" required>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Birthday</label>
                            <input type="date" name="birth" value="<?= esc($user['birth']) ?>" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Gender</label>
                            <select name="gender" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                                <option value="Male" <?= ($user['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($user['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($user['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%">Save Changes</button>
                    </div>
                </form>
                
                <div class="danger-zone" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                    <h4 style="color: red; margin-bottom: 10px;">Danger Zone</h4>
                    <form action="<?= base_url('user/delete_account') ?>" method="post" onsubmit="return confirm('Are you sure?');">
                        <?= csrf_field() ?>
                        <div style="display: flex; gap: 10px;">
                            <input type="password" name="password" placeholder="Enter password to confirm" required style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            <button type="submit" class="btn btn-danger" style="background: red; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">Delete</button>
                        </div>
                    </form>
                </div>

            </div> </div>
    </div>

    <div id="reactorsModal" class="modal-overlay" style="display: none; align-items: center; justify-content: center; z-index: 2000;">
        <div class="modal-content" style="width: 400px; max-height: 80vh; display: flex; flex-direction: column; background: white; border-radius: 8px;">
            
            <div class="modal-header" style="border-bottom: 1px solid #ddd; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px;">People who reacted</h3>
                <button onclick="document.getElementById('reactorsModal').style.display='none'" 
                        style="background: none; border: none; cursor: pointer; padding: 5px;">
                    <i data-lucide="x"></i>
                </button>
=======
                <div id="posts-container" class="tab-content active">
                    <?php if($isOwnProfile): ?>
                        <?= view('components/post/create_post', ['user' => $user, 'avatarUrl' => $avatarUrl]) ?>
                    <?php endif; ?>

                    <?php foreach ($posts as $post): ?>
                        <?= view('components/post/post_item', ['post' => $post, 'avatarUrl' => $avatarUrl, 'context' => 'profile']) ?>
                    <?php endforeach; ?>
                </div>

                <div id="friends-container" class="tab-content">
                    <div class="friends-grid">
                        <?php if(!empty($friends)): foreach($friends as $f): ?>
                            <div class="fb-card friend-card">
                                <div class="card-content">
                                    <img src="<?= $f['avatar'] ? base_url($f['avatar']) : base_url('ICON/avatar-default.svg') ?>" class="card-avatar">
                                    <span class="friend-name"><?= esc($f['firstname'].' '.$f['lastname']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
>>>>>>> admin
            </div>

            <div id="reactors-list" style="overflow-y: auto; padding: 10px; flex: 1;">
                </div>
        </div>
    </div>

    <div id="editPostModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between;">
                <h3 style="margin:0">Edit Post</h3>
                <div onclick="document.getElementById('editPostModal').style.display='none'" style="cursor:pointer"><i data-lucide="x"></i></div>
            </div>
            <form id="editPostForm" onsubmit="submitEditPost(event)">
                <?= csrf_field() ?>
                <div class="modal-body" style="padding: 15px;">
                    <textarea name="content" id="edit-content" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; resize: none;"></textarea>
                    <select name="privacy" id="edit-privacy" style="margin-top: 10px; padding: 5px; width: 100%;">
                        <option value="public">Public</option>
                        <option value="friends">Friends</option>
                        <option value="private">Only Me</option>
                    </select>
                </div>
                <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #ddd;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD

    <script src="<?= base_url('JS/navbar.js') ?>"></script>
    <script> 
        lucide.createIcons(); 
        document.addEventListener('DOMContentLoaded', function() {
        loadPage(1, true); 
    });
=======
    <script>
        const BASE_URL = "<?= rtrim(base_url(), '/') ?>";
        lucide.createIcons();
>>>>>>> admin
    </script>
    <script src="<?= base_url('JS/post/utils.js') ?>"></script>
    <script src="<?= base_url('JS/post/reactions.js') ?>"></script>
    <script src="<?= base_url('JS/post/comments.js') ?>"></script>
    <script src="<?= base_url('JS/post.js') ?>"></script>
</body>
</html>