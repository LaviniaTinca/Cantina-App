<aside class="sidebar">
  <ul>
    <li class="title-dashboard <?php if ($current_page == 'admin.php') echo 'active'; ?>"><a href="admin.php"><i class="bx bxs-dashboard"></i>Dashboard</a></li>
    <br>
    <hr>
    </li>
    <li>
      <a href="#"><i class="bx bxs-cart-alt"></i>Ecommerce</a>
      <ul>
        <li <?php if ($current_page == 'admin_users.php') echo 'class="active"'; ?>><a href="admin_users.php"><i class="bx bxs-user"></i>Users</a></li>
        <li <?php if ($current_page == 'admin_view_products.php') echo 'class="active"'; ?>><a href="admin_view_products.php" <?php if ($current_page == 'admin_view_products.php') echo 'class="active"'; ?>><i class="bx bx-package"></i>Products</a></li>
        <li><a href="#"><i class="bx bx-category"></i>Category</a></li>
        <li><a href="admin_orders.php"><i class="bx bx-receipt"></i>Orders</a></li>
      </ul>
    </li>

    <li><a href="#"><i class="bx bx-line-chart"></i>Analytics</a>
      <ul>
        <li><a href="#"><i class="bx bxs-pie-chart-alt-2"></i>Sales Chart</a></li>
        <li><a href="#"><i class="bx bxs-pie-chart-alt-2"></i>Product Chart</a></li>
        <li><a href="#"><i class="bx bx-bar-chart"></i>Statistics</a></li>
      </ul>
    </li>
    <li><a href="#"><i class="bx bx-envelope"></i>Mail</a></li>
    <li><a href="#"><i class="bx bx-group"></i>Subscribers</a></li>
    <li><a href="#"><i class="bx bx-cog"></i>Settings</a></li>
    <li><a href="help.php"><i class="bx bx-info-circle"></i>Help & Support</a></li>
  </ul>
  <div class="logout">
    <a href="logout.php"><i class="bx bx-run" style="margin-left: 5px"></i>Logout</a>
  </div>
</aside>