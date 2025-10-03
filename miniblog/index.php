<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Ensure base URL
$base_url = $base_url ?? 'http://localhost/miniBlogApp/miniblog';

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // 3 per row for nicer grid
$offset = ($page - 1) * $limit;

// Search
$search = $_GET['search'] ?? '';

// Fetch posts with category
$sql = "SELECT p.*, c.name AS category_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.title LIKE :search1 OR p.content LIKE :search2
        ORDER BY p.created_at DESC
        LIMIT $offset, $limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search1', "%$search%");
$stmt->bindValue(':search2', "%$search%");
$stmt->execute();
$posts = $stmt->fetchAll();

// Total posts count
$total_sql = "SELECT COUNT(*) FROM posts WHERE title LIKE :search1 OR content LIKE :search2";
$total_stmt = $pdo->prepare($total_sql);
$total_stmt->bindValue(':search1', "%$search%");
$total_stmt->bindValue(':search2', "%$search%");
$total_stmt->execute();
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Base URL for uploads
$uploads_path = $base_url . '/uploads';
?>

<?php include 'includes/header.php'; ?>

<style>
/* Uniform image height */
.card-img-top {
    width: 100%;
    height: 200px; /* Set uniform height */
    object-fit: cover; /* Crops and fills nicely */
}

/* Make all cards same height */
.card.h-100 {
    display: flex;
    flex-direction: column;
}
.card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.card-text {
    flex-grow: 1;
}
</style>

<div class="container my-4">
    <h2 class="mb-4">All Posts</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3 d-flex">
        <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>" class="form-control me-2">
        <button class="btn btn-primary">Search</button>
    </form>

    <div class="row">
        <?php if ($posts): ?>
            <?php foreach($posts as $p): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($p['thumbnail'])): ?>
                            <img src="<?= $uploads_path ?>/<?= htmlspecialchars($p['thumbnail']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['title']) ?>">
                        <?php else: ?>
                            <img src="<?= $base_url ?>/assets/images/default.png" class="card-img-top" alt="No Image">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
                            <p class="card-text"><?= substr(strip_tags($p['content']), 0, 120) ?>...</p>
                            <p class="text-muted mt-auto mb-2">
                                Category: <?= htmlspecialchars($p['category_name']) ?><br>
                                Views: <?= $p['views'] ?><br>
                                Posted on: <?= date('d M Y', strtotime($p['created_at'])) ?>
                            </p>
                            <a href="post.php?id=<?= $p['id'] ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
