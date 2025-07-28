<?php
session_start();
include 'db.php';

if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    unset($_SESSION['toast']);
}

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$offset = ($page - 1) * $limit;
$search_sql = $search ? "WHERE post LIKE '%$search%' OR username LIKE '%$search%'" : '';

$total_result = $conn->query("SELECT COUNT(*) as total FROM posts $search_sql");
$total = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM posts $search_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/d9fcdfbh7jyu9cuu64ro4sftx7tnkrqiy0ge8n9990beuqye/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: 'textarea.rich-text',
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link',
        menubar: false,
        height: 200
      });
    </script>
</head>
<body class="container mt-4">

<!-- Top Right Buttons -->
<div class="d-flex justify-content-end mb-3">
    <?php if (!isset($_SESSION['username'])): ?>
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <a href="register.php" class="btn btn-primary">Register</a>
    <?php else: ?>
        <span class="me-3">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    <?php endif; ?>
</div>

<!-- Search Bar -->
<form method="GET" class="mb-4 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-outline-primary">Search</button>
</form>

<!-- Toast -->
<?php if (isset($toast)): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div class="toast show align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= htmlspecialchars($toast) ?></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Create Post Button -->
<?php if (isset($_SESSION['username'])): ?>
    <div class="mb-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPostModal">➕ Create Post</button>
    </div>
<?php endif; ?>

<h3>📃 Posts</h3>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5><?= htmlspecialchars($row['username']) ?></h5>
            <div><?= $row['post'] ?></div>

            <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $row['username']): ?>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">✏️ Edit</button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">🗑️ Delete</button>
            <?php endif; ?>

            <small class="text-muted">Created: <?= $row['created_at'] ?> | Updated: <?= $row['updated_at'] ?></small>
        </div>
    </div>

    <!-- Edit Modal -->
    <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $row['username']): ?>
    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
      <div class="modal-dialog">
        <form action="edit.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
              <textarea name="post" class="form-control rich-text" rows="4" required><?= htmlspecialchars($row['post']) ?></textarea>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
      <div class="modal-dialog">
        <form action="delete.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel<?= $row['id'] ?>">Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
              <p>Are you sure you want to delete this post?</p>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Delete</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>
<?php endwhile; ?>

<!-- Pagination -->
<nav>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">« Prev</a></li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next »</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="add.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createPostModalLabel">Create New Blog Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>">
          <div class="mb-3">
              <label for="postContent" class="form-label">Post Content</label>
              <textarea name="post" class="form-control rich-text" rows="4" required></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Post</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
