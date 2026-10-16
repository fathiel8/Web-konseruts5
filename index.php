
<?php
session_start();

// PAGE VIEW COUNTER
$counterFile = __DIR__ . '/counter.txt';

if (!file_exists($counterFile)) {
    file_put_contents($counterFile, '0');
}

$counter = fopen($counterFile, 'c+');
flock($counter, LOCK_EX);
$views = (int) trim(stream_get_contents($counter));
$views++;
rewind($counter);
ftruncate($counter, 0);
fwrite($counter, (string)$views);
fflush($counter);
flock($counter, LOCK_UN);
fclose($counter);

// DATA TIKET SIMULASI
$tickets = [
    'festival' => [
        'name' => 'Festival',
        'price' => 850000,
        'description' => 'Feel the music. Feel the crowd.'
    ],
    'gold' => [
        'name' => 'Gold',
        'price' => 1450000,
        'description' => 'A closer view of the stage.'
    ],
    'platinum' => [
        'name' => 'Platinum',
        'price' => 2250000,
        'description' => 'The ultimate concert experience.'
    ]
];

function rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$errors = [];
$booking = null;
$keyword = trim($_GET['q'] ?? '');

// PENCARIAN TIKET MENGGUNAKAN GET
$filteredTickets = [];

foreach ($tickets as $key => $ticket) {
    if (
        $keyword === '' ||
        stripos($ticket['name'], $keyword) !== false
    ) {
        $filteredTickets[$key] = $ticket;
    }
}

// PROSES BOOKING MENGGUNAKAN POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['book_ticket'])) {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $category = $_POST['category'] ?? '';
    $quantity = filter_var(
        $_POST['quantity'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (strlen($name) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    if (!isset($tickets[$category])) {
        $errors[] = 'Pilih kategori tiket yang tersedia.';
    }

    if ($quantity === false ||
        $quantity === null ||
        $quantity < 1 ||
        $quantity > 6) {
        $errors[] = 'Jumlah tiket harus 1 sampai 6.';
    }

    if (!isset($_POST['agree'])) {
        $errors[] = 'Centang persetujuan simulasi booking.';
    }

    if (empty($errors)) {
        $booking = [
            'code' => 'JM-' . strtoupper(bin2hex(random_bytes(3))),
            'name' => $name,
            'email' => $email,
            'category' => $tickets[$category]['name'],
            'quantity' => $quantity,
            'total' => $tickets[$category]['price'] * $quantity
        ];

        $_SESSION['booking'] = $booking;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>John Mayer Live | Concert Experience</title>

    <link rel="stylesheet" href="style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap"
          rel="stylesheet">
</head>

<body>

<header class="navbar">
    <a href="#home" class="brand">
        <span class="brand-logo">JM</span>
        <span>
            JOHN MAYER
            <small>LIVE EXPERIENCE</small>
        </span>
    </a>

    <button class="menu-toggle"
            aria-label="Buka navigasi"
            aria-expanded="false">☰</button>

    <nav class="nav-links">
        <a href="#home">Home</a>
        <a href="#experience">Experience</a>
        <a href="#tickets">Tickets</a>
        <a href="#venue">Venue</a>
        <a href="#booking">Booking</a>
    </nav>

    <a href="#tickets" class="nav-button">Get Tickets ↗</a>
</header>

<main>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-shade"></div>

    <div class="hero-content">
        <p class="eyebrow">
            <span class="live-dot"></span>
            A LIVE MUSIC EXPERIENCE
        </p>

        <h1>
            One night.<br>
            <em>Endless feeling.</em>
        </h1>

        <p class="hero-description">
            Let the guitar speak. Feel every lyric.<br>
            Make a memory that stays with you.
        </p>

        <div class="hero-actions">
            <a href="#tickets" class="button button-primary">
                Explore Tickets <span>↗</span>
            </a>

            <a href="#experience" class="text-link">
                Discover the experience ↓
            </a>
        </div>

        <div class="hero-caption">
            THE SOUNDTRACK TO YOUR STORY
        </div>
    </div>

    <div class="hero-bottom">
        <span>INTIMATE. ELECTRIC. UNFORGETTABLE.</span>
        <span>SCROLL TO EXPLORE ↓</span>
    </div>
</section>

<!-- EVENT INFORMATION -->
<section class="event-info">
    <div class="info-item">
        <span class="info-icon">◷</span>
        <div>
            <small>DATE</small>
            <strong>To be announced</strong>
        </div>
    </div>

    <div class="info-item">
        <span class="info-icon">⌖</span>
        <div>
            <small>LOCATION</small>
            <strong>Jakarta, Indonesia</strong>
        </div>
    </div>

    <div class="info-item">
        <span class="info-icon">♫</span>
        <div>
            <small>MUSIC GENRE</small>
            <strong>Pop · Blues · Rock</strong>
        </div>
    </div>

    <div class="demo-label">
        <span></span> EDUCATIONAL DEMO WEBSITE
    </div>
</section>

<!-- EXPERIENCE -->
<section class="section experience" id="experience">
    <div class="section-heading">
        <p class="eyebrow dark-eyebrow">MORE THAN A CONCERT</p>
        <h2>Feel the music.<br><em>Keep the moment.</em></h2>
        <p>
            Celebrate the magic of live music through an
            unforgettable concert concept.
        </p>
    </div>

    <div class="experience-grid">
        <div class="experience-image">
            <span class="image-label">01 / THE SOUND</span>
            <h3>Every note,<br>closer.</h3>
        </div>

        <div class="experience-details">
            <span class="number">01</span>

            <h3>A night built<br>around the music.</h3>

            <p>
                From soulful guitar solos to songs that feel
                like old friends, experience a night designed
                for people who truly love music.
            </p>

            <div class="feature">
                <span>✦</span>
                <div>
                    <strong>Live music atmosphere</strong>
                    <small>Feel the energy of every performance.</small>
                </div>
            </div>

            <div class="feature">
                <span>✦</span>
                <div>
                    <strong>Your own concert experience</strong>
                    <small>Choose the ticket that fits your night.</small>
                </div>
            </div>

            <a href="#tickets" class="inline-link">
                Find your ticket →
            </a>
        </div>
    </div>
</section>

<!-- QUOTE -->
<section class="quote-section">
    <span class="quote-mark">“</span>

    <h2>
        Some songs don't just play.<br>
        They <em>take you somewhere.</em>
    </h2>

    <p>THE MAGIC OF LIVE MUSIC</p>
</section>

<!-- TICKET CATEGORIES -->
<section class="section tickets-section" id="tickets">
    <div class="section-heading centered">
        <p class="eyebrow dark-eyebrow">CHOOSE YOUR EXPERIENCE</p>
        <h2>Your night,<br><em>your point of view.</em></h2>
        <p>Temukan kategori tiket yang cocok untukmu.</p>
    </div>

    <form action="#tickets" method="GET" class="search-form">
        <input type="search"
               name="q"
               value="<?= e($keyword) ?>"
               placeholder="Cari Festival, Gold, Platinum...">

        <button type="submit">⌕</button>
    </form>

    <div class="ticket-grid">

        <?php foreach ($filteredTickets as $key => $ticket): ?>

        <article class="ticket-card <?= $key === 'gold' ? 'featured' : '' ?>">

            <?php if ($key === 'gold'): ?>
                <span class="popular-label">MOST POPULAR</span>
            <?php endif; ?>

            <div class="ticket-symbol">
                <?= $key === 'festival' ? '✳' : ($key === 'gold' ? '✦' : '✧') ?>
            </div>

            <p class="ticket-category">TICKET CATEGORY</p>

            <h3><?= e($ticket['name']) ?></h3>

            <p class="ticket-description">
                <?= e($ticket['description']) ?>
            </p>

            <div class="ticket-price">
                <?= rupiah($ticket['price']) ?>
                <small>/ person</small>
            </div>

            <ul class="ticket-benefits">
                <li>✓ Booking summary</li>
                <li>✓ Digital registration preview</li>
                <li>✓ Event information</li>
            </ul>

            <a href="#booking"
               class="button <?= $key === 'gold' ? 'button-primary' : 'button-outline' ?>"
               data-category="<?= e($key) ?>">
                Choose <?= e($ticket['name']) ?> →
            </a>
        </article>

        <?php endforeach; ?>

        <?php if (empty($filteredTickets)): ?>
            <p class="no-results">
                Kategori tidak ditemukan. Coba Festival, Gold, atau Platinum.
            </p>
        <?php endif; ?>

    </div>

    <p class="disclaimer">
        *Harga dan detail event merupakan simulasi untuk tugas pembelajaran.
    </p>
</section>

<!-- VENUE -->
<section class="venue-section" id="venue">
    <div class="venue-content">
        <p class="eyebrow">THE PERFECT SETTING</p>

        <h2>Meet us where<br>the music lives.</h2>

        <p>Jakarta, Indonesia · Venue to be announced</p>

        <div class="venue-detail">
            <span>⌖</span>
            <div>
                <strong>Location</strong>
                <small>Detail venue belum ditetapkan.</small>
            </div>
        </div>

        <div class="venue-detail">
            <span>◷</span>
            <div>
                <strong>Event schedule</strong>
                <small>Tanggal konser belum ditetapkan.</small>
            </div>
        </div>
    </div>

    <div class="venue-art">
        <div class="venue-circle"></div>
        <div class="venue-circle circle-large"></div>

        <div class="venue-art-text">
            LET THE<br>
            <em>MUSIC</em><br>
            TAKE OVER.
        </div>

        <span class="venue-art-caption">
            LIVE · LOUD · IN THE MOMENT
        </span>
    </div>
</section>

<!-- BOOKING FORM -->
<section class="section booking-section" id="booking">

    <div class="booking-intro">
        <p class="eyebrow dark-eyebrow">SAVE YOUR SPOT</p>

        <h2>Make it<br><em>your night.</em></h2>

        <p>
            Isi formulir untuk mencoba proses registrasi
            tiket dan melihat ringkasan pesanan.
        </p>

        <div class="booking-note">
            <span>✦</span>
            <p>
                <strong>Booking made simple.</strong><br>
                <small>
                    Pemesanan ini hanya simulasi.
                    Tidak ada pembayaran sungguhan.
                </small>
            </p>
        </div>
    </div>

    <div class="form-container">

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Periksa kembali data kamu:</strong>

                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($booking): ?>

            <div class="success-box">
                <div class="success-icon">✓</div>

                <p class="eyebrow dark-eyebrow">REGISTRATION PREVIEW</p>
                <h3>You're on the list.</h3>

                <p>
                    Terima kasih, <?= e($booking['name']) ?>.
                    Data registrasi berhasil diproses.
                </p>

                <div class="receipt">
                    <div>
                        <span>Kode booking</span>
                        <strong><?= e($booking['code']) ?></strong>
                    </div>

                    <div>
                        <span>Nama</span>
                        <strong><?= e($booking['name']) ?></strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong><?= e($booking['email']) ?></strong>
                    </div>

                    <div>
                        <span>Kategori</span>
                        <strong><?= e($booking['category']) ?></strong>
                    </div>

                    <div>
                        <span>Jumlah tiket</span>
                        <strong><?= (int)$booking['quantity'] ?></strong>
                    </div>

                    <div class="receipt-total">
                        <span>Total simulasi</span>
                        <strong><?= rupiah($booking['total']) ?></strong>
                    </div>
                </div>

                <p class="disclaimer">
                    Ini bukan tiket masuk resmi dan bukan bukti pembayaran.
                </p>

                <a href="#tickets" class="button button-primary">
                    Explore tickets →
                </a>
            </div>

        <?php else: ?>

            <form method="POST" action="#booking" class="booking-form">

                <div class="form-heading">
                    <span>01 — YOUR DETAILS</span>
                    <h3>Booking information</h3>
                </div>

                <label for="name">Full name</label>
                <input type="text"
                       id="name"
                       name="name"
                       minlength="3"
                       maxlength="100"
                       required
                       placeholder="Masukkan nama lengkap"
                       value="<?= e($_POST['name'] ?? '') ?>">

                <label for="email">Email address</label>
                <input type="email"
                       id="email"
                       name="email"
                       required
                       placeholder="nama@email.com"
                       value="<?= e($_POST['email'] ?? '') ?>">

                <div class="form-row">
                    <div>
                        <label for="category">Ticket category</label>

                        <select name="category" id="category" required>
                            <option value="">Pilih kategori</option>

                            <?php foreach ($tickets as $key => $ticket): ?>
                                <option value="<?= e($key) ?>"
                                    <?= (($_POST['category'] ?? '') === $key) ? 'selected' : '' ?>>
                                    <?= e($ticket['name']) ?>
                                    — <?= rupiah($ticket['price']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="quantity">Quantity</label>

                        <select name="quantity" id="quantity">
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>"
                                    <?= ((string)($_POST['quantity'] ?? '1') === (string)$i) ? 'selected' : '' ?>>
                                    <?= $i ?> tiket
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="total-preview">
                    <span>Estimated total</span>
                    <strong id="totalPreview">Pilih tiket</strong>
                </div>

                <label class="agree-label">
                    <input type="checkbox"
                           name="agree"
                           required
                           <?= isset($_POST['agree']) ? 'checked' : '' ?>>

                    <span>
                        Saya memahami bahwa ini merupakan simulasi
                        pemesanan untuk proyek pembelajaran.
                    </span>
                </label>

                <button type="submit"
                        name="book_ticket"
                        value="1"
                        class="button button-primary submit-button">
                    Preview Booking ↗
                </button>

                <p class="form-footnote">
                    Tidak ada pembayaran atau penerbitan tiket resmi.
                </p>
            </form>

        <?php endif; ?>
    </div>
</section>

</main>

<!-- FOOTER -->
<footer class="footer">
    <a href="#home" class="brand footer-brand">
        <span class="brand-logo">JM</span>
        <span>
            JOHN MAYER
            <small>LIVE EXPERIENCE</small>
        </span>
    </a>

    <p>A concert website concept for an educational project.</p>

    <div class="page-counter">
        <span>PAGE VIEWS</span>
        <strong><?= number_format($views, 0, ',', '.') ?></strong>
        <small>Counted with PHP</small>
    </div>

    <div class="footer-bottom">
        <span>© <?= date('Y') ?> John Mayer Live Concept</span>
        <a href="#home">Back to top ↑</a>
        <span>NOT AN OFFICIAL EVENT WEBSITE</span>
    </div>
</footer>

<script>
const menuButton = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

menuButton.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('nav-open');
    menuButton.setAttribute('aria-expanded', String(isOpen));
});

document.querySelectorAll('[data-category]').forEach(button => {
    button.addEventListener('click', () => {
        const category = document.querySelector('#category');
        if (category) category.value = button.dataset.category;
        updateTotal();
    });
});

const prices = {
    festival: 850000,
    gold: 1450000,
    platinum: 2250000
};

const categorySelect = document.querySelector('#category');
const quantitySelect = document.querySelector('#quantity');
const totalPreview = document.querySelector('#totalPreview');

function updateTotal() {
    if (!categorySelect || !quantitySelect || !totalPreview) return;

    const price = prices[categorySelect.value];

    totalPreview.textContent = price
        ? 'Rp ' + (price * Number(quantitySelect.value))
            .toLocaleString('id-ID')
        : 'Pilih tiket';
}

categorySelect?.addEventListener('change', updateTotal);
quantitySelect?.addEventListener('change', updateTotal);

updateTotal();
</script>

</body>
</html>
