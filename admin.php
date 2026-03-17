<?php
session_start();

if (!isset($_SESSION['zenobia_admin']) || $_SESSION['zenobia_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$contentFile = __DIR__ . '/content/site.json';
$content = [
    'hero' => [],
    'about' => [],
    'booking' => [],
    'contact' => [],
    'images' => [],
    'menu' => []
];

if (file_exists($contentFile)) {
    $decoded = json_decode(file_get_contents($contentFile), true);
    if (is_array($decoded)) {
        $content = array_merge($content, $decoded);
    }
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_content') {
    $menuJson = $_POST['menu_json'] ?? '';
    $menuData = [];
    if ($menuJson !== '') {
        $menuData = json_decode($menuJson, true);
        if ($menuData === null && json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Menu JSON is invalid: ' . json_last_error_msg();
        }
    }

    if ($error_message === '') {
        $content = [
            'hero' => [
                'kicker' => $_POST['hero_kicker'] ?? '',
                'title' => $_POST['hero_title'] ?? '',
                'subtitle' => $_POST['hero_subtitle'] ?? ''
            ],
            'about' => [
                'title' => $_POST['about_title'] ?? '',
                'body' => $_POST['about_body'] ?? ''
            ],
            'booking' => [
                'title' => $_POST['booking_title'] ?? '',
                'intro' => $_POST['booking_intro'] ?? ''
            ],
            'contact' => [
                'phone' => $_POST['contact_phone'] ?? '',
                'email' => $_POST['contact_email'] ?? '',
                'address' => $_POST['contact_address'] ?? '',
                'instagram' => $_POST['contact_instagram'] ?? ''
            ],
            'images' => [
                'heroBackground' => $_POST['img_hero'] ?? '',
                'aboutImage' => $_POST['img_about'] ?? '',
                'parallaxImage' => $_POST['img_parallax'] ?? '',
                'bookingImage' => $_POST['img_booking'] ?? ''
            ],
            'menu' => $menuData
        ];

        file_put_contents($contentFile, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $success_message = 'Content saved successfully.';
    }
}

function h($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenobia Admin</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-shell">
        <header class="admin-header">
            <h1>Zenobia Admin</h1>
            <a href="logout.php" class="secondary" style="text-decoration:none;"><button type="button" class="secondary">Logout</button></a>
        </header>

        <main class="admin-main">
            <section class="card">
                <?php if ($success_message): ?>
                    <p class="status"><?php echo h($success_message); ?></p>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <p class="error"><?php echo h($error_message); ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="save_content">

                    <nav class="tabs">
                        <button type="button" class="tab active" data-tab="hero">Hero</button>
                        <button type="button" class="tab" data-tab="about">About</button>
                        <button type="button" class="tab" data-tab="booking">Booking</button>
                        <button type="button" class="tab" data-tab="contact">Contact</button>
                        <button type="button" class="tab" data-tab="images">Images</button>
                        <button type="button" class="tab" data-tab="menu">Menu</button>
                    </nav>

                    <div id="tab-hero" class="tab-panel active">
                        <h2>Hero</h2>
                        <label for="hero_kicker">Kicker</label>
                        <input type="text" id="hero_kicker" name="hero_kicker" value="<?php echo h($content['hero']['kicker'] ?? ''); ?>">

                        <label for="hero_title">Title</label>
                        <input type="text" id="hero_title" name="hero_title" value="<?php echo h($content['hero']['title'] ?? ''); ?>">

                        <label for="hero_subtitle">Subtitle</label>
                        <textarea id="hero_subtitle" name="hero_subtitle" rows="3"><?php echo h($content['hero']['subtitle'] ?? ''); ?></textarea>
                    </div>

                    <div id="tab-about" class="tab-panel">
                        <h2>About</h2>
                        <label for="about_title">Title</label>
                        <input type="text" id="about_title" name="about_title" value="<?php echo h($content['about']['title'] ?? ''); ?>">

                        <label for="about_body">Body</label>
                        <textarea id="about_body" name="about_body" rows="8"><?php echo h($content['about']['body'] ?? ''); ?></textarea>
                    </div>

                    <div id="tab-booking" class="tab-panel">
                        <h2>Booking</h2>
                        <label for="booking_title">Title</label>
                        <input type="text" id="booking_title" name="booking_title" value="<?php echo h($content['booking']['title'] ?? ''); ?>">

                        <label for="booking_intro">Intro</label>
                        <textarea id="booking_intro" name="booking_intro" rows="4"><?php echo h($content['booking']['intro'] ?? ''); ?></textarea>
                    </div>

                    <div id="tab-contact" class="tab-panel">
                        <h2>Contact</h2>
                        <label for="contact_phone">Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="<?php echo h($content['contact']['phone'] ?? ''); ?>">

                        <label for="contact_email">Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="<?php echo h($content['contact']['email'] ?? ''); ?>">

                        <label for="contact_address">Address</label>
                        <textarea id="contact_address" name="contact_address" rows="3"><?php echo h($content['contact']['address'] ?? ''); ?></textarea>

                        <label for="contact_instagram">Instagram</label>
                        <input type="text" id="contact_instagram" name="contact_instagram" value="<?php echo h($content['contact']['instagram'] ?? ''); ?>">
                    </div>

                    <div id="tab-images" class="tab-panel">
                        <h2>Images</h2>
                        <label for="img_hero">Hero background</label>
                        <input type="text" id="img_hero" name="img_hero" value="<?php echo h($content['images']['heroBackground'] ?? 'img/6.jpg'); ?>">

                        <label for="img_about">About image</label>
                        <input type="text" id="img_about" name="img_about" value="<?php echo h($content['images']['aboutImage'] ?? 'img/2.jpg'); ?>">

                        <label for="img_parallax">Parallax image</label>
                        <input type="text" id="img_parallax" name="img_parallax" value="<?php echo h($content['images']['parallaxImage'] ?? 'img/5.jpg'); ?>">

                        <label for="img_booking">Booking image</label>
                        <input type="text" id="img_booking" name="img_booking" value="<?php echo h($content['images']['bookingImage'] ?? 'img/9.jpg'); ?>">
                    </div>

                    <div id="tab-menu" class="tab-panel">
                        <h2>Menu</h2>
                        <p class="hint">
                            Add, edit, or remove menu sections, groups, and items. Changes here
                            update the menu JSON automatically when you save.
                        </p>
                        <div id="menu-builder" class="menu-builder">
                            <div id="menu-sections"></div>
                            <button type="button" id="add-section-btn" class="add-btn">
                                + Add Section
                            </button>
                        </div>
                        <!-- hidden JSON field used by PHP, filled by JS on save -->
                        <input type="hidden" id="menu_json" name="menu_json" value="<?php echo h(json_encode($content['menu'] ?? [], JSON_UNESCAPED_SLASHES)); ?>">
                    </div>

                    <button type="submit">Save changes</button>
                </form>
            </section>
        </main>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const tabs = document.querySelectorAll(".tab");
        const panels = document.querySelectorAll(".tab-panel");

        tabs.forEach((btn) => {
          btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-tab");
            tabs.forEach((b) => b.classList.toggle("active", b === btn));
            panels.forEach((panel) =>
              panel.classList.toggle("active", panel.id === "tab-" + id)
            );
          });
        });

        // --- Menu builder ---
        const menuJsonInput = document.getElementById("menu_json");
        const sectionsContainer = document.getElementById("menu-sections");
        const addSectionBtn = document.getElementById("add-section-btn");
        const form = document.querySelector("form");

        let menuData = [];
        try {
          menuData = JSON.parse(menuJsonInput.value || "[]");
        } catch {
          menuData = [];
        }

        function createItemRow(item = {}) {
          const wrap = document.createElement("div");
          wrap.className = "menu-item-block";
          wrap.innerHTML = `
            <div class="menu-item-row">
              <input type="text" class="menu-item-name-input" placeholder="Name" value="${(item.name || "").replace(/"/g, "&quot;")}">
              <input type="text" class="menu-item-price-input" placeholder="Price" value="${(item.price || "").replace(/"/g, "&quot;")}">
              <button type="button" class="remove-item-btn">×</button>
            </div>
            <textarea class="menu-item-desc-input" rows="2" placeholder="Description">${item.description || ""}</textarea>
          `;
          wrap.querySelector(".remove-item-btn").addEventListener("click", () => {
            wrap.remove();
          });
          return wrap;
        }

        function createGroupBlock(group = {}) {
          const block = document.createElement("div");
          block.className = "menu-group-block";
          block.innerHTML = `
            <div class="menu-group-header">
              <input type="text" class="menu-group-title-input" placeholder="Group title (e.g. Cold Mezze)" value="${(group.title || "").replace(/"/g, "&quot;")}">
              <button type="button" class="remove-group-btn">×</button>
            </div>
            <div class="menu-items"></div>
            <button type="button" class="add-item-btn">+ Add Item</button>
          `;
          const itemsContainer = block.querySelector(".menu-items");
          (group.items || []).forEach((it) => {
            itemsContainer.appendChild(createItemRow(it));
          });
          block.querySelector(".add-item-btn").addEventListener("click", () => {
            itemsContainer.appendChild(createItemRow());
          });
          block.querySelector(".remove-group-btn").addEventListener("click", () => {
            block.remove();
          });
          return block;
        }

        function createSectionBlock(section = {}) {
          const block = document.createElement("div");
          block.className = "menu-section-block";
          block.innerHTML = `
            <div class="menu-section-header">
              <input type="text" class="menu-section-title-input" placeholder="Section title (e.g. Starters & Mezze)" value="${(section.title || "").replace(/"/g, "&quot;")}">
              <button type="button" class="remove-section-btn">×</button>
            </div>
            <div class="menu-groups"></div>
            <button type="button" class="add-group-btn">+ Add Group</button>
          `;
          const groupsContainer = block.querySelector(".menu-groups");
          (section.groups || []).forEach((g) => {
            groupsContainer.appendChild(createGroupBlock(g));
          });
          block.querySelector(".add-group-btn").addEventListener("click", () => {
            groupsContainer.appendChild(createGroupBlock());
          });
          block.querySelector(".remove-section-btn").addEventListener("click", () => {
            block.remove();
          });
          return block;
        }

        function renderMenuBuilder() {
          if (!sectionsContainer) return;
          sectionsContainer.innerHTML = "";
          if (!Array.isArray(menuData) || !menuData.length) {
            sectionsContainer.appendChild(createSectionBlock());
            return;
          }
          menuData.forEach((section) => {
            sectionsContainer.appendChild(createSectionBlock(section));
          });
        }

        function collectMenuFromUI() {
          const sections = [];
          sectionsContainer.querySelectorAll(".menu-section-block").forEach((sec) => {
            const title = sec.querySelector(".menu-section-title-input").value.trim();
            const groups = [];
            sec.querySelectorAll(".menu-group-block").forEach((grp) => {
              const gTitle = grp.querySelector(".menu-group-title-input").value.trim();
              const items = [];
              grp.querySelectorAll(".menu-item-block").forEach((it) => {
                const name = it.querySelector(".menu-item-name-input").value.trim();
                const price = it.querySelector(".menu-item-price-input").value.trim();
                const desc = it.querySelector(".menu-item-desc-input").value.trim();
                if (name) {
                  items.push({ name, price, description: desc });
                }
              });
              if (gTitle && items.length) {
                groups.push({ title: gTitle, items });
              }
            });
            if (title && groups.length) {
              sections.push({ title, groups });
            }
          });
          return sections;
        }

        if (addSectionBtn && sectionsContainer) {
          addSectionBtn.addEventListener("click", () => {
            sectionsContainer.appendChild(createSectionBlock());
          });
          renderMenuBuilder();
        }

        if (form && menuJsonInput) {
          form.addEventListener("submit", () => {
            const sections = collectMenuFromUI();
            menuJsonInput.value = JSON.stringify(sections);
          });
        }
      });
    </script>
</body>
</html>

