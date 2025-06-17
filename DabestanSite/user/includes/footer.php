<?php
// File: DabestanSite/user/includes/footer.php (Master Layout Footer)
?>
            </main> <!-- End of .page-content -->
        </div> <!-- End of .main-content -->
        <div class="overlay" id="overlay"></div>
    </div> <!-- End of .user-layout -->

    <script>
        function updateLiveTime() {
            const timeElement = document.getElementById('live-time');
            if (timeElement) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateLiveTime, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const overlay = document.getElementById('overlay');
            const isDesktop = window.innerWidth > 768;

            function toggleSidebar() {
                if (isDesktop) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('full-width');
                } else {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                }
                if (isDesktop) {
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            }

            if (isDesktop && localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('full-width');
            }

            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });

            overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>