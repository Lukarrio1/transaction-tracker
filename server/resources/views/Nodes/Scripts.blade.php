
<script>
    const types = @json($types)


    const node_type = document.querySelector('#node_type')

    setTimeout(() => {
        if (@json($node) != null) {
            // Set the select's value
            node_type.value = @json($node)['node_type']['value'];
            // Optional: Trigger an event to simulate user interaction (e.g., change)
            node_type.dispatchEvent(new Event('change'));

        }
    }, 1000)



    const extra_fields = document.querySelector('#extra_fields')

    if (node_type)
        node_type.addEventListener('change', function(event) {
            // Get the selected option
            const selectedOption = node_type.options[node_type.selectedIndex];
            // Access the data attributes
            const customValue = selectedOption.getAttribute('data-node-type');
            const selected = customValue
            const current_type = types[selected]
            extra_fields.innerHTML = current_type ? .extra_html ? current_type ? .extra_html : ''



        });

</script>
