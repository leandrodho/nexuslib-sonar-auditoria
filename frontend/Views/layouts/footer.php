	<script>
		function goToSearch(query) {
			const origin = document.querySelector('input[name="origin"]:checked')?.value || '';
			const params = new URLSearchParams();
			if (query) params.append('q', query);
			if (origin) params.append('origin', origin);
			// Use client-side viewUrl helper; when adding query params to an existing view_url() output, use '&'
			window.location.href = viewUrl('search/results') + (params.toString() ? '&' + params.toString() : '');
		}
	</script>
</body>
</html>
<?php
