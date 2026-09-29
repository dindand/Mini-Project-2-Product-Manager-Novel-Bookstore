<?php
// public/edit.php - mini_projectdnd
session_start();
require_once __DIR__ . "/../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

if (!$id) {
    header("Location: index.php?status=notfound");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(["id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php?status=notfound");
    exit;
}

$name = $product["name"];
$brand = $product["brand"] ?? "Penulis Indonesia";
$category = $product["category"];
$price = $product["price"];
$stock = $product["stock"];
$imageUrl = $product["image_url"] ?? "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $brand = trim($_POST["brand"] ?? "Penulis Indonesia");
    $category = trim($_POST["category"] ?? "Fiksi");
    $priceRaw = trim($_POST["price"] ?? "");
    $stockRaw = trim($_POST["stock"] ?? "");
    $imageUrl = trim($_POST["image_url"] ?? "");

    $price = filter_var($priceRaw, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($stockRaw, FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) {
        $errors["name"] = "Judul novel minimal harus terdiri dari 3 karakter.";
    } else {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name) = LOWER(:name) AND id != :id");
        $stmtCheck->execute(["name" => $name, "id" => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors["name"] = "Judul novel ini sudah digunakan oleh entri lain.";
        }
    }

    if (empty($brand)) { $brand = "Penulis Indonesia"; }
    if (empty($category)) { $category = "Fiksi"; }

    if ($price === false || $price <= 0) {
        $errors["price"] = "Harga buku harus berupa nominal lebih besar dari 0.";
    }

    if ($stock === false || $stock < 0) {
        $errors["stock"] = "Jumlah stok tidak boleh bernilai negatif.";
    }

    if (empty($errors)) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE products SET name = :name, brand = :brand, category = :category, price = :price, stock = :stock, image_url = :image_url WHERE id = :id");
            $stmtUpdate->execute([
                "name"      => $name,
                "brand"     => $brand,
                "category"  => $category,
                "price"     => $price,
                "stock"     => $stock,
                "image_url" => !empty($imageUrl) ? $imageUrl : null,
                "id"        => $id,
            ]);

            header("Location: index.php?status=updated");
            exit;
        } catch (PDOException $e) {
            $errors["general"] = "Gagal memperbarui data novel: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Novel - DnD Bookstore</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <div class="brand-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">DnD Bookstore</span>
                    <span class="brand-subtitle">Inventaris Novel & Karya Sastra Best Seller Indonesia</span>
                </div>
            </a>
            <a href="index.php" class="btn btn-secondary">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Katalog</span>
            </a>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">Edit Data Novel</h1>
                <p class="form-subtitle">Perbarui informasi novel ID #<?= (int)$product["id"] ?></p>
            </div>

            <?php if (!empty($errors["general"])): ?>
                <div class="alert alert-error">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span><?= htmlspecialchars($errors["general"], ENT_QUOTES, "UTF-8") ?></span>
                </div>
            <?php endif; ?>

            <form action="edit.php?id=<?= (int)$id ?>" method="POST" novalidate>
                <input type="hidden" name="id" value="<?= (int)$product["id"] ?>">

                <div class="form-group">
                    <label for="name" class="form-label">Judul Lengkap Novel <span style="color: var(--accent-rose);">*</span></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        value="<?= htmlspecialchars((string)$name, ENT_QUOTES, "UTF-8") ?>"
                        required
                    >
                    <?php if (isset($errors["name"])): ?>
                        <div class="form-error-msg"><?= htmlspecialchars($errors["name"], ENT_QUOTES, "UTF-8") ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="brand" class="form-label">Nama Penulis / Pengarang <span style="color: var(--accent-rose);">*</span></label>
                        <input 
                            type="text" 
                            id="brand" 
                            name="brand" 
                            class="form-input" 
                            value="<?= htmlspecialchars((string)$brand, ENT_QUOTES, "UTF-8") ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="category" class="form-label">Genre / Kategori</label>
                        <select id="category" name="category" class="form-select">
                            <option value="Petualangan & Alam" <?= $category === 'Petualangan & Alam' ? 'selected' : '' ?>>Petualangan & Alam</option>
                            <option value="Fiksi Sejarah & Sastra" <?= $category === 'Fiksi Sejarah & Sastra' ? 'selected' : '' ?>>Fiksi Sejarah & Sastra</option>
                            <option value="Drama & Inspirasi" <?= $category === 'Drama & Inspirasi' ? 'selected' : '' ?>>Drama & Inspirasi</option>
                            <option value="Fantasi & Petualangan" <?= $category === 'Fantasi & Petualangan' ? 'selected' : '' ?>>Fantasi & Petualangan</option>
                            <option value="Romance & Drama" <?= $category === 'Romance & Drama' ? 'selected' : '' ?>>Romance & Drama</option>
                            <option value="Sci-Fi & Romance" <?= $category === 'Sci-Fi & Romance' ? 'selected' : '' ?>>Sci-Fi & Romance</option>
                            <option value="Aksi & Thriller" <?= $category === 'Aksi & Thriller' ? 'selected' : '' ?>>Aksi & Thriller</option>
                            <option value="Fiksi" <?= $category === 'Fiksi' ? 'selected' : '' ?>>Fiksi Umum</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="price" class="form-label">Harga Buku (Rp) <span style="color: var(--accent-rose);">*</span></label>
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            class="form-input" 
                            step="any"
                            value="<?= htmlspecialchars((string)$price, ENT_QUOTES, "UTF-8") ?>"
                            required
                        >
                        <?php if (isset($errors["price"])): ?>
                            <div class="form-error-msg"><?= htmlspecialchars($errors["price"], ENT_QUOTES, "UTF-8") ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="stock" class="form-label">Stok Eksemplar <span style="color: var(--accent-rose);">*</span></label>
                        <input 
                            type="number" 
                            id="stock" 
                            name="stock" 
                            class="form-input" 
                            value="<?= htmlspecialchars((string)$stock, ENT_QUOTES, "UTF-8") ?>"
                            required
                        >
                        <?php if (isset($errors["stock"])): ?>
                            <div class="form-error-msg"><?= htmlspecialchars($errors["stock"], ENT_QUOTES, "UTF-8") ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image_url" class="form-label">URL Cover Buku</label>
                    <input 
                        type="url" 
                        id="image_url" 
                        name="image_url" 
                        class="form-input" 
                        placeholder="https://images.unsplash.com/..." 
                        value="<?= htmlspecialchars((string)$imageUrl, ENT_QUOTES, "UTF-8") ?>"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>Praktikum Pemrograman Web &bull; Pertemuan 3 &bull; DnD Bookstore Management</p>
    </footer>

</body>
</html>
