# TODO: Create Newsfeed Route and View

## Steps to Complete:
- [x] Create resources/views/newsfeed.blade.php: Adapt the provided HTML to display dynamic posts in a timeline/newsfeed layout using Blade templating.
- [x] Update app/Http/Controllers/PostController.php: Add newsfeed() method to fetch posts with relationships and pass to the view.
- [x] Update routes/web.php: Add GET /newsfeed route pointing to PostController@newsfeed, protected by auth middleware.
- [x] Test the new route by running the application and verifying the newsfeed displays posts correctly.
- [x] Fix routing to timeline and add a button in the dashboard for the timeline.
- [x] Add image upload functionality to posts.
