<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsfeed</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/newsfeed.css') }}" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="content" class="content content-full-width">
                <!-- begin profile -->
                <div class="profile">
                    <div class="profile-header">
                        <!-- BEGIN profile-header-cover -->
                        <div class="profile-header-cover"></div>
                        <!-- END profile-header-cover -->
                        <!-- BEGIN profile-header-content -->
                        <div class="profile-header-content">
                            <!-- BEGIN profile-header-img -->
                            <div class="profile-header-img">
                                <img src="{{ Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar3.png' }}" alt="">
                            </div>
                            <!-- END profile-header-img -->
                            <!-- BEGIN profile-header-info -->
                            <div class="profile-header-info">
                                <h4 class="m-t-10 m-b-5">{{ Auth::user()->name }}</h4>
                                <p class="m-b-10">Newsfeed</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-info mb-2">Dashboard</a>
                            </div>
                            <!-- END profile-header-info -->
                        </div>
                        <!-- END profile-header-content -->
                    </div>
                </div>
                <!-- end profile -->
                <!-- begin profile-content -->
                <div class="profile-content">
                    <!-- begin tab-content -->
                    <div class="tab-content p-0">
                        <!-- begin #profile-post tab -->
                        <div class="tab-pane fade active show" id="profile-post">
                            <!-- begin timeline -->
                            <ul class="timeline">
                                @forelse($posts as $index => $post)
                                <li class="{{ $index % 2 == 0 ? '' : 'timeline-inverted' }}">
                                    <!-- begin timeline-time -->
                                    <div class="timeline-time">
                                        <span class="date">{{ $post->created_at->format('d M Y') }}</span>
                                        <span class="time">{{ $post->created_at->format('H:i') }}</span>
                                    </div>
                                    <!-- end timeline-time -->
                                    <!-- begin timeline-icon -->
                                    <div class="timeline-icon">
                                        <a href="javascript:;">&nbsp;</a>
                                    </div>
                                    <!-- end timeline-icon -->
                                    <!-- begin timeline-body -->
                                    <div class="timeline-body">
                                        <div class="timeline-header">
                                            <span class="userimage"><img src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar3.png' }}" alt=""></span>
                                            <span class="username"><a href="javascript:;">{{ $post->user->name }}</a></span>
                                            <span class="pull-right text-muted">{{ $post->total_comments_count }} Comments</span>
                                        </div>
                                        <div class="timeline-content">
                                            <p>{{ $post->content }}</p>
                                            @if($post->image)
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="img-fluid mt-2">
                                            @endif
                                        </div>
                                        <div class="timeline-likes">
                                            <div class="stats-right">
                                                <span class="stats-text">{{ $post->total_comments_count }} Comments</span>
                                            </div>
                                            <div class="stats">
                                                <span class="fa-stack fa-fw stats-icon">
                                                <i class="fa fa-circle fa-stack-2x text-danger"></i>
                                                <i class="fa fa-heart fa-stack-1x fa-inverse t-plus-1"></i>
                                                </span>
                                                <span class="fa-stack fa-fw stats-icon">
                                                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                                                <i class="fa fa-thumbs-up fa-stack-1x fa-inverse"></i>
                                                </span>
                                                <span class="stats-total">{{ $post->upvotes_count + $post->downvotes_count }}</span>
                                            </div>
                                        </div>
                                        <div class="timeline-footer">
                                            <a href="javascript:;" class="m-r-15 text-inverse-lighter"><i class="fa fa-thumbs-up fa-fw fa-lg m-r-3"></i> Like</a>
                                            <a href="javascript:;" class="m-r-15 text-inverse-lighter"><i class="fa fa-comments fa-fw fa-lg m-r-3"></i> Comment</a>
                                            <a href="javascript:;" class="m-r-15 text-inverse-lighter"><i class="fa fa-share fa-fw fa-lg m-r-3"></i> Share</a>
                                        </div>
                                        <!-- Comments Section -->
                                        @if($post->comments->count() > 0)
                                        <div class="timeline-comment-box">
                                            @foreach($post->comments as $comment)
                                            <div class="user"><img src="{{ $comment->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar3.png' }}"></div>
                                            <div class="input">
                                                <p><strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}</p>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    <!-- end timeline-body -->
                                </li>
                                @empty
                                <li>
                                    <div class="timeline-body">
                                        <p>No posts available.</p>
                                    </div>
                                </li>
                                @endforelse
                            </ul>
                            <!-- end timeline -->
                        </div>
                        <!-- end #profile-post tab -->
                    </div>
                    <!-- end tab-content -->
                </div>
                <!-- end profile-content -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
</body>
</html>
