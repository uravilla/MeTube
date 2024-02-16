<?php
session_start(); ?>
<html>

<head>
    <title>Media Upload</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="default.css" />

</head>

<body>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <a class="navbar-brand" href="browse.php"><img src="img/icon_metube.png" style="width:65px;" alt="logo"></a>
        <ul class="navbar-nav">
            <li class="nav-item" style="float:right"><a class="nav-link" href="update.php">Welcome <?php $username = $_SESSION['username'];
                                                                                                    echo "$username" ?> </a></li>
            <li class="nav-item">
                <a class="nav-link" href="update.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="wordcloud.php">Word Cloud</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="media_upload.php">Upload File</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="browse.php">Back</a>
            </li>
            <li class="nav-item">
                <!-- Search form -->
                <div class="search-container">
                    <form action="browsefilter.php" method="post">
                        <input type="text" id="searchwords" name="searchwords" size="60" placeholder="Search Keywords">
                        <input type="submit" name="submit" value="Search">
                    </form>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <br>
    <h4>Upload File</h4>
    <br>
    <form method="post" action="media_upload_prc.php" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
        <label for="file">Add Media <label style="color:#663399"><em>(Each file limit 10MB):</em></label></label>
        <input type="file" name="file" id="file" /><br><br>
        Title:<input type="text" name="title" maxlength="20" /><br><br>
        <label for="description">Description:</label>
        <input type="text" name="description" id="description" /><br><br>
        <label for="category">Category:</label>
        <select name="category" id="category">
            <option value="image">Image</option>
            <option value="video">Video</option>
            <option value="audio">Audio</option>
        </select><br><br>
        <label for="share">Sharing Mode:</label>
        <select name="share" id="share">
            <option value="public">Public</option>
            <option value="me">Only me</option>
            <option value="family">Family</option>
            <option value="friends">Friends</option>
            <option value="favourites">Favourites</option>
        </select> <br><br>
        <label for="discussion">Allow Discussion:</label>
        <select name="discussion" id="discussion">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>
        <label for="rating">Allow Rating:</label>
        <select name="rating" id="rating">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select><br><br>
        <label for="keywords">Keywords:</label>
        <textarea name="keywords" rows="5" cols="30" placeholder="Enter the Keywords seperated by commas (,)."></textarea><br>
        <br><br><input type="submit" value="Upload" name="submit" />
    </form>
</body>

</html>