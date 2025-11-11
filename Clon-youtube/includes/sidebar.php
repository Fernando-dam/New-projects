<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item active">
            <i class="fa-solid fa-house"></i>
            <span>Inicio</span>
        </a>
        <a href="trending.php" class="nav-item">
            <i class="fa-solid fa-fire-flame-curved"></i>
            <span>Tendencias</span>
        </a>
        <a href="subscriptions.php" class="nav-item">
            <i class="fa-solid fa-rectangle-list"></i>
            <span>Suscripciones</span>
        </a>
        
        <hr>
        
        <a href="library.php" class="nav-item">
            <i class="fa-solid fa-folder-open"></i>
            <span>Biblioteca</span>
        </a>
        <a href="history.php" class="nav-item">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Historial</span>
        </a>
        <a href="liked.php" class="nav-item">
            <i class="fa-solid fa-thumbs-up"></i>
            <span>Videos que te gustaron</span>
        </a>
        <a href="watchlater.php" class="nav-item">
            <i class="fa-regular fa-clock"></i>
            <span>Ver más tarde</span>
        </a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <hr>
        <h3 class="sidebar-title">Suscripciones</h3>
        <?php
        $query = "SELECT u.id, u.channel_name, u.channel_avatar 
                  FROM subscriptions s 
                  JOIN users u ON s.channel_id = u.id 
                  WHERE s.subscriber_id = ? 
                  ORDER BY u.channel_name";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($subscriptions as $sub):
        ?>
        <a href="channel.php?id=<?php echo $sub['id']; ?>" class="nav-item subscription">
            <img src="<?php echo htmlspecialchars($sub['channel_avatar']); ?>" alt="" class="subscription-avatar">
            <span><?php echo htmlspecialchars($sub['channel_name']); ?></span>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
        
        <hr>
        
        <h3 class="sidebar-title">Explorar</h3>
        <a href="category.php?c=music" class="nav-item">
            <i class="fa-solid fa-music"></i>
            <span>Música</span>
        </a>
        <a href="category.php?c=sports" class="nav-item">
            <i class="fa-solid fa-futbol"></i>
            <span>Deportes</span>
        </a>
        <a href="category.php?c=gaming" class="nav-item">
            <i class="fa-solid fa-gamepad"></i>
            <span>Videojuegos</span>
        </a>
        <a href="category.php?c=news" class="nav-item">
            <i class="fa-solid fa-newspaper"></i>
            <span>Noticias</span>
        </a>
        <a href="category.php?c=live" class="nav-item">
            <i class="fa-solid fa-tower-broadcast"></i>
            <span>En directo</span>
        </a>
    </nav>
</aside><aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="trending.php" class="nav-item">
            <i class="fas fa-fire"></i>
            <span>Tendencias</span>
        </a>
        <a href="subscriptions.php" class="nav-item">
            <i class="fas fa-play-circle"></i>
            <span>Suscripciones</span>
        </a>
        
        <hr>
        
        <a href="library.php" class="nav-item">
            <i class="fas fa-folder"></i>
            <span>Biblioteca</span>
        </a>
        <a href="history.php" class="nav-item">
            <i class="fas fa-history"></i>
            <span>Historial</span>
        </a>
        <a href="liked.php" class="nav-item">
            <i class="fas fa-thumbs-up"></i>
            <span>Videos que te gustaron</span>
        </a>
        <a href="watchlater.php" class="nav-item">
            <i class="fas fa-clock"></i>
            <span>Ver más tarde</span>
        </a>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <hr>
        <h3 class="sidebar-title">Suscripciones</h3>
        <?php
        $query = "SELECT u.id, u.channel_name, u.channel_avatar 
                  FROM subscriptions s 
                  JOIN users u ON s.channel_id = u.id 
                  WHERE s.subscriber_id = ? 
                  ORDER BY u.channel_name";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($subscriptions as $sub):
        ?>
        <a href="channel.php?id=<?php echo $sub['id']; ?>" class="nav-item subscription">
            <img src="<?php echo htmlspecialchars($sub['channel_avatar']); ?>" alt="" class="subscription-avatar">
            <span><?php echo htmlspecialchars($sub['channel_name']); ?></span>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
        
        <hr>
        
        <h3 class="sidebar-title">Explorar</h3>
        <a href="category.php?c=music" class="nav-item">
            <i class="fas fa-music"></i>
            <span>Música</span>
        </a>
        <a href="category.php?c=sports" class="nav-item">
            <i class="fas fa-futbol"></i>
            <span>Deportes</span>
        </a>
        <a href="category.php?c=gaming" class="nav-item">
            <i class="fas fa-gamepad"></i>
            <span>Videojuegos</span>
        </a>
        <a href="category.php?c=news" class="nav-item">
            <i class="fas fa-newspaper"></i>
            <span>Noticias</span>
        </a>
        <a href="category.php?c=live" class="nav-item">
            <i class="fas fa-broadcast-tower"></i>
            <span>En directo</span>
        </a>
    </nav>
</aside>