<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Post</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css">
  <style>
    body { background:#0f0f10; color:#fff; }
    .card { background: #1f1f20; border-color: #3a3a3c; }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="mb-0">Edit Post</h3>
          <a href="{{ route('timeline') }}" class="btn btn-outline-light btn-sm">Back</a>
        </div>

        <div class="card-body">
          {{-- UPDATE FORM --}}
          <form id="update-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
              <label for="content">Content</label>
              <textarea class="form-control" id="content" name="content" rows="3" required>{{ old('content', $post->content) }}</textarea>
              @error('content') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
              <label for="image">Replace media (optional)</label>
              <input type="file" class="form-control-file" id="image" name="image" accept="image/*,video/*,image/gif">
              @error('image') <small class="text-danger">{{ $message }}</small> @enderror
              <small class="form-text text-muted">Allowed: jpeg,png,jpg,gif,mp4,mov,avi,webm. Max 20MB.</small>
            </div>

            @if ($post->image)
              <div class="mb-3">
                <label class="d-block text-muted">Current media</label>
                @if($post->media_type === 'video')
                  <video controls class="w-100 rounded">
                    <source src="{{ asset('storage/' . $post->image) }}" type="video/{{ pathinfo($post->image, PATHINFO_EXTENSION) }}">
                    Your browser does not support the video tag.
                  </video>
                @else
                  <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded">
                @endif
              </div>
            @endif

            <div class="d-flex">
              <button type="submit" class="btn btn-primary mr-2" style="background:#ff0b55;border:none;">Update Post</button>
              <a href="{{ route('timeline') }}" class="btn btn-secondary ml-auto">Cancel</a>
            </div>
          </form>

          {{-- DELETE FORM (SEPARATE, NOT NESTED) --}}
          <div class="mt-3">
            <form id="delete-post-form" action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post? This cannot be undone.');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">Delete Post</button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
