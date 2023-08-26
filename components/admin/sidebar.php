<aside class="sidebar">
  <ul>
    <li class="title-dashboard <?php if ($current_page == 'admin.php') echo 'active'; ?>">
      <a href="admin.php">
        <i class="bx bxs-dashboard"></i>
        Dashboard
      </a>
    </li>
    <br>
    <hr>

    <li <?php if ($current_page == 'admin_users.php' or $current_page == 'admin_edit_user.php') echo 'class="active"'; ?>>
      <a href="admin_users.php">
        <i class="bx bxs-user"></i>
        Utilizatori
      </a>
    </li>
    <li <?php if ($current_page == 'admin_products.php' or $current_page == 'admin_view_product.php' or $current_page == 'admin_edit_product.php') echo 'class="active"'; ?>>
      <a href="admin_products.php">
        <i class="bx bx-package"></i>
        Produse
      </a>
    </li>
    <li <?php if ($current_page == 'admin_menu.php') echo 'class="active"'; ?>>
      <a href="admin_menu.php">
        <i class="bx bx-category"></i>
        Meniul zilei
      </a>
    </li>
    <li <?php if ($current_page == 'admin_orders.php') echo 'class="active"'; ?>>
      <a href="admin_orders.php">
        <i class="bx bx-receipt"></i>
        Comenzi
      </a>
    </li>
    <li <?php if ($current_page == 'admin_messages.php') echo 'class="active"'; ?>>
      <a href="admin_messages.php">
        <i class="bx bx-envelope"></i>
        Mesagerie
      </a>
    </li>
    <li <?php if ($current_page == 'admin_subscribers.php') echo 'class="active"'; ?>>
      <a href="admin_subscribers.php">
        <i class="bx bx-group"></i>
        Abonați
      </a>
    </li>
    <li <?php if ($current_page == 'admin_announcements.php') echo 'class="active"'; ?>>
      <a href="admin_announcements.php">
        <i class="bx bx-cog"></i>
        Anunțuri
      </a>
    </li>
    <!-- <li <?php if ($current_page == 'help.php') echo 'class="active"'; ?>>
      <a href="help.php">
        <i class="bx bx-info-circle"></i>
        Help & Support
      </a>
    </li> -->

    <li <?php if ($current_page == 'logout.php') echo 'class="active"'; ?>>
      <div class="logout">
        <a href="../pages/logout.php"><i class="bx bx-run"></i>Deconectare</a>
      </div>
    </li>
  </ul>
</aside>