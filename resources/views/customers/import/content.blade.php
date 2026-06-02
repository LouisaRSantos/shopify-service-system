<div class="main-panel" id="ajax-content">

    <div class="content-wrapper">

        <div class="row">

            <div class="col-md-12 grid-margin">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">
                            Import Customers
                        </h4>

                        <p class="card-description">
                            Upload a CSV file to import customers into Shopify
                        </p>

                        <form id="importForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>CSV File</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>

                            <div class="mt-3">
                                <a href="/customers/import/template" class="btn btn-light">
                                    Download Template
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Import Customers
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.getElementById("importForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("/customers/import/process", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.created !== undefined) {
            alert(`Import Completed: ${data.created} created`);

            // optional: replace alert with Skydash toast
            // showToast("success", "Import completed");
        }

        if (data.failed && data.failed.length > 0) {
            console.log("Failed rows:", data.failed);
        }

    })
    .catch(err => {
        console.error(err);
        alert("Import failed");
    });
});
</script>