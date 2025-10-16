<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Timeline / Newsfeed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Bootstrap 4 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    {{-- Custom --}}
    <link href="{{ asset('assets/css/newsfeed.css') }}" rel="stylesheet">
    <style>
        body { background: #fff; color: #000; font-family: Arial, sans-serif; }

        /* Profile Header from Newsfeed */
        .profile-header-cover { background: #FF0B55; height: 180px; border-radius: 0 0 15px 15px; }
        .profile-header-img img { width: 120px; height: 120px; border-radius: 50%; border: 4px solid #fff; }
        .profile-header-info h4, .profile-header-info p { color: #fff; }

        /* Exit Button */
        .btn-outline-light {
            position: absolute; top: 20px; right: 20px;
            border: 2px solid #fff; color: #fff; background: none;
            border-radius: 6px; padding: 6px 14px; font-weight: bold;
        }
        .btn-outline-light:hover { background: #fff; color: #FF0B55; }

        /* Post Widget */
        .widget { background:#fff; border:1px solid #ddd; border-radius:6px; box-shadow:0 2px 10px rgba(0,0,0,.05); margin-bottom:20px; }
        .widget-header { padding:1rem; display:flex; align-items:center; }
        .widget-body { padding:1rem; }
        .widget-footer { background:#f8f9fa; padding:.75rem 1rem; }

        /* Comments */
        .comments-section { background:#f9f9f9; border-top:1px solid #eee; display:none; padding:0.5rem 1rem; }
        .comment-input { width:100%; }
        .timeline-time { font-size: 13px; color:#888; }

        /* Media preview */
        #mediaPreview { display:none; text-align:center; }
        #mediaPreview img, #mediaPreview video { max-height:300px; border-radius:10px; }
        #removePreview { position:absolute; top:-10px; right:-10px; border-radius:50%; width:25px; height:25px; line-height:1; font-weight:bold; }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">

            {{-- Profile Section --}}
            <div class="profile">
                <div class="profile-header">
                    <div class="profile-header-cover"></div>
                    <div class="profile-header-content text-center position-relative">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light"><i class="la la-sign-out"></i> Exit</a>
                        
                        <form id="avatarForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="profile-header-img mt-n5">
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" id="avatarPreview">
                                <div class="camera-overlay" onclick="document.getElementById('avatarInput').click();">
                                    <i class="la la-camera"></i>
                                </div>
                            </div>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit()">
                        </form>

                        <div class="profile-header-info mt-3">
                            <h4>{{ Auth::user()->name }}</h4>
                            <p>Welcome to your Newsfeed</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Post Form --}}
            <div class="card my-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?"></textarea>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label class="mb-0 mr-2">Attach</label>
                                <input type="file" id="mediaInput" name="image" accept="image/*,video/*,image/gif" class="form-control-file">
                            </div>
                            <button type="submit" class="btn btn-primary" style="background-color:#FF0B55; border:none;">Post</button>
                        </div>

                        <div id="mediaPreview" class="mt-3 position-relative">
                            <button type="button" id="removePreview" class="btn btn-sm btn-danger">×</button>
                            <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded" style="display:none;">
                            <video id="previewVideo" controls class="w-100 rounded" style="display:none;">
                                <source id="previewVideoSource" src="#">
                            </video>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            <ul class="timeline list-unstyled">
                @forelse($posts as $post)
                <li class="mb-4">
                    <div class="timeline-time mb-2">
                        <span class="date">{{ $post->created_at->format('M d, Y') }}</span> —
                        <span class="time">{{ $post->created_at->format('H:i') }}</span>
                    </div>

                    <div class="widget" id="post-{{ $post->id }}">
                        <div class="widget-header">
                            <img src="{{ $post->user->avatar_url }}" class="rounded-circle" width="40" height="40" style="object-fit:cover;">
                            <div class="ml-2">
                                <strong>{{ $post->user->name }}</strong>
                                <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                            </div>

                            @if(auth()->id() === $post->user_id)
                            <div class="ml-auto dropdown">
                                <a href="#" class="text-dark" data-toggle="dropdown"><i class="la la-ellipsis-h"></i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('posts.edit', $post->id) }}"><i class="la la-edit"></i> Edit</a>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="delete-post-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger delete-post-btn" data-id="{{ $post->id }}"><i class="la la-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="widget-body">
                            <p>{{ $post->content }}</p>
                            @if($post->image)
                                @if($post->media_type === 'video')
                                    <video controls class="w-100 rounded mt-2"><source src="{{ asset('storage/' . $post->image) }}"></video>
                                @else
                                    <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mt-2">
                                @endif
                            @endif
                        </div>

                        <div class="widget-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="#" class="upvote-btn {{ $post->userVote(auth()->id())==='up'?'voted-up':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-up"></i></a>
                                    <span id="upvote-count-{{ $post->id }}">{{ $post->upvotes()->count() }}</span>

                                    <a href="#" class="downvote-btn ml-3 {{ $post->userVote(auth()->id())==='down'?'voted-down':'' }}" data-id="{{ $post->id }}"><i class="la la-arrow-down"></i></a>
                                    <span id="downvote-count-{{ $post->id }}">{{ $post->downvotes()->count() }}</span>
                                </div>
                                <div>
                                    <a href="#" class="toggle-comments" data-id="{{ $post->id }}"><i class="la la-comment"></i> <span id="comment-count-{{ $post->id }}">{{ $post->total_comments_count }}</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="comments-section" id="comments-section-{{ $post->id }}">
                            <div class="comments-list mb-2">
                                @foreach($post->comments as $comment)
                                <div class="comment mb-2 d-flex" id="comment-{{ $comment->id }}">
                                    <img src="{{ $comment->user->avatar_url }}" class="rounded-circle mr-2" width="30" height="30" style="object-fit:cover;">
                                    <div>
                                        <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
                                        <div class="replies ml-4 mt-1">
                                            @foreach($comment->replies as $reply)
                                            <div class="comment mb-1 d-flex" id="comment-{{ $reply->id }}">
                                                <img src="{{ $reply->user->avatar_url }}" class="rounded-circle mr-2" width="25" height="25" style="object-fit:cover;">
                                                <div><strong>{{ $reply->user->name }}</strong>: {{ $reply->content }}</div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-danger comment-send" data-id="{{ $post->id }}">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li><div class="text-center text-muted">No posts yet.</div></li>
                @endforelse
            </ul>
            <div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Keep your timeline.js logic intact --}}
<script>
    // === (Use same JS logic from your timeline.blade, unchanged) ===
</script>
</body>
</html>
