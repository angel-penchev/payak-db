<script src="assets/js/components/card.js"></script>

<main>
  <p>Database connection status: 
    <?php echo isset($pdo) ? "<strong style='color:green'>Connected!</strong>" : "Failed"; ?>
  </p>
  <p>This structure is ready for Windows XAMPP deployment.</p>

  <div class="p-10 max-w-md mx-auto bg-red-100">
    <ui-card>
      <ui-card-header>
        <ui-card-title>Login</ui-card-title>
        <ui-card-description>Sign-in your account.</ui-card-description>
      </ui-card-header>

      <ui-card-content>
        <p class="text-xl">
          gosho
        </p>
      </ui-card-content>

      <ui-card-footer>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
         Login 
        </button>
      </ui-card-footer>
    </ui-card>
  </div>
</main>
