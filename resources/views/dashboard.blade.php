<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Publ Feed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
    	
body{margin-top:20px;}				              
    </style>
</head>
<body>
<!-- Header -->
<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
  <div class="container bootstrap snippets bootdey">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-toggle"></span>
      </button>
      <a class="navbar-brand" href="#">Application</a>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        
        <li class="dropdown">
          <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">
            <i class="glyphicon glyphicon-user"></i> {{ Auth::user()->name }} <span class="caret"></span></a>
          <ul id="g-account-menu" class="dropdown-menu" role="menu">
            <li><a href="#">My Profile</a></li>
            <li>
                <form method="POST" action="/logout" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; padding: 0;">
                        <i class="glyphicon glyphicon-lock"></i> Logout
                    </button>
                </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- /Header -->

<!-- Main -->
<div class="container bootstrap snippets bootdey">
  <div class="row">
    <div class="col-md-3">
      <a href="#"><strong><i class="glyphicon glyphicon-briefcase"></i> Toolbox</strong></a>
      <hr>
      <ul class="nav nav-pills nav-stacked">
        <li><a href="#"><i class="glyphicon glyphicon-flash"></i> Alerts</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-link"></i> Links</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-list-alt"></i> Reports</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-book"></i> Books</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-briefcase"></i> Tools</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-time"></i> Real-time</a></li>
        <li><a href="#"><i class="glyphicon glyphicon-plus"></i> Advanced..</a></li>
      </ul>
      <hr>
    </div>

    <div class="col-md-9">
      <a href="#"><strong><i class="glyphicon glyphicon-dashboard"></i> My Dashboard</strong></a>
      <hr>
      <div class="row">
        <div class="col-md-7">
          <div class="well">Inbox Messages <span class="badge pull-right">3</span></div>
          <hr>
          <div class="panel panel-default">
            <div class="panel-heading"><h4>Processing Status</h4></div>
            <div class="panel-body">
              <small>Complete</small>
              <div class="progress">
                <div class="progress-bar progress-bar-success" style="width: 72%"></div>
              </div>
              <small>In Progress</small>
              <div class="progress">
                <div class="progress-bar progress-bar-info" style="width: 20%"></div>
              </div>
              <small>At Risk</small>
              <div class="progress">
                <div class="progress-bar progress-bar-danger" style="width: 80%"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <ul class="nav nav-justified">
            <li><a href="#"><i class="glyphicon glyphicon-cog"></i></a></li>
            <li><a href="#"><i class="glyphicon glyphicon-heart"></i></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-comment"></i><span class="count">3</span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">1. Is there a way..</a></li>
                <li><a href="#">2. Hello, admin. I would..</a></li>
                <li><a href="#"><strong>All messages</strong></a></li>
              </ul>
            </li>
            <li><a href="#"><i class="glyphicon glyphicon-user"></i></a></li>
            <li><a title="Add Widget" data-toggle="modal" href="#addWidgetModal"><span class="glyphicon glyphicon-plus-sign"></span></a></li>
          </ul>
          <hr>
          <p>
            This is a responsive dashboard layout using <a href="https://www.getbootstrap.com">Bootstrap 3</a>. 
            You can use this template as a base to build something more unique.
          </p>
          <hr>
          <div class="btn-group btn-group-justified">
            <a href="#" class="btn btn-info col-sm-3"><i class="glyphicon glyphicon-plus"></i><br>Service</a>
            <a href="#" class="btn btn-info col-sm-3"><i class="glyphicon glyphicon-cloud"></i><br>Cloud</a>
            <a href="#" class="btn btn-info col-sm-3"><i class="glyphicon glyphicon-cog"></i><br>Tools</a>
            <a href="#" class="btn btn-info col-sm-3"><i class="glyphicon glyphicon-question-sign"></i><br>Help</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
</body>
</html>