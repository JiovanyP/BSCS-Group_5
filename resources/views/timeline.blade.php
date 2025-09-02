<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Timeline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">
    <style>
        body {
            margin-top: 20px;
            background: #eee;
        }

        .timeline {
            width: 100%;
            position: relative;
            padding: 1px 0;
            list-style: none;
            font-weight: 500;
        }

        .timeline .timeline-item {
            position: relative;
            float: left;
            clear: left;
            width: 50%;
            margin-bottom: 20px;
        }

        .timeline .timeline-item:before,
        .timeline .timeline-item:after {
            content: "";
            display: table;
        }

        .timeline .timeline-item:after {
            clear: both;
        }

        .timeline .timeline-item>.timeline-event {
            position: relative;
            float: left;
        }

        .timeline .timeline-item>.timeline-point {
            color: #5d5386;
            background: #5d5386;
            right: -14px;
            width: 12px;
            height: 12px;
            margin-top: -6px;
            margin-left: 8px;
            margin-right: 8px;
            position: absolute;
            z-index: 100;
            border-width: 3px;
            border-style: solid;
            border-radius: 100%;
            line-height: 20px;
            text-align: center;
            box-shadow: 0 0 0 5px #f2f3f8;
        }

        .timeline:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 50%;
            margin-left: 2px;
            border-right-width: 4px;
            border-right-style: solid;
            border-right-color: rgba(52, 40, 104, .1);
        }

        .timeline .timeline-label {
            position: relative;
            float: left;
            clear: left;
            width: 50%;
            margin-bottom: 20px;
            top: 1px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding: 0;
            text-align: center;
        }

        .timeline .timeline-label .label {
            background-color: #4facfe;
            border-radius: 35px;
            color: #fff;
            display: inline;
            font-size: .85rem;
            font-weight: 600;
            line-height: 1;
            padding: .65rem 1.4rem;
            text-align: center;
            vertical-align: baseline;
            white-space: nowrap;
        }

        .widget {
            background: #fff;
            border-radius: 4px;
            border: none;
            margin-bottom: 30px;
        }

        .widget-header {
            background: #fff;
            padding: .85rem 1.4rem;
            position: relative;
            width: 100%;
        }

        .widget-body {
            padding: 1.4rem;
        }

        .widget-footer {
            background: #fff;
            padding: 1rem 1.07rem;
            position: relative;
        }

        .users-like a img {
            width: 40px;
            border: .25rem solid #fff;
            margin-right: -10px;
        }

        .meta ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        .meta ul li {
            margin-right: 0.5rem;
        }

        .meta ul li a {
            color: rgba(52, 40, 104, .3);
        }

        .meta ul li a:hover {
            color: rgba(52, 40, 104, .9);
        }

        @media screen and (max-width:768px) {
            .timeline .timeline-item {
                width: 100%;
                margin-bottom: 20px;
            }

            .timeline:before {
                left: 42px;
                width: 0;
            }

            .timeline .timeline-item>.timeline-point {
                transform: translateX(-50%);
                left: 42px!important;
                margin-left: 0;
            }

            .timeline .timeline-label {
                transform: translateX(-50%);
                margin: 0 0 20px 42px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-12">

            {{-- Post Form --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('timeline.store') }}" method="POST">
                        @csrf
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?" required></textarea>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>

            {{-- Show Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="timeline timeline-line-solid">
                <span class="timeline-label">
                    <span class="label bg-primary">Timeline</span>
                </span>

                {{-- Loop through posts --}}
                @forelse ($posts as $post)
                    <div class="timeline-item">
                        <div class="timeline-point timeline-point"></div>
                        <div class="timeline-event">
                            <div class="widget has-shadow">
                                <div class="widget-header d-flex align-items-center">
                                    <div class="user-image">
                                        {{-- Smaller avatar --}}
                                            <img class="rounded-circle" src="{{ $post->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png' }}" alt="..." style="width:40px; height:40px;">

                                    </div>
                                    <div class="d-flex flex-column mr-auto ml-2">
                                        <div class="title">
                                            <span class="username">{{ $post->user->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-body">
                                    <p>{{ $post->content }}</p>
                                </div>
                                <div class="widget-footer d-flex align-items-center">
                                    <div class="col no-padding d-flex justify-content-start">
                                        <div class="meta">
                                            <ul>
                                                <li>
                                                    <a href="#" class="like-btn" data-id="{{ $post->id }}">
                                                        <i class="la la-heart"></i>
                                                        <span class="numb">{{ $post->likes_count ?? 0 }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="comment-btn" data-id="{{ $post->id }}">
                                                        <i class="la la-comment"></i>
                                                        <span class="numb">{{ $post->comments_count ?? 0 }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
        </div>
        <div class="time-right">{{ $post->created_at->diffForHumans() }}</div>
    </div>
</div>
                @empty
                    <p class="text-center text-muted">No posts yet.</p>
                @endforelse

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
