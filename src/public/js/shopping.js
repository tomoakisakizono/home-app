document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("#shopping-form").addEventListener("submit", function(event) {
        event.preventDefault(); // ページ遷移しない
        let formData = new FormData(this);

        fetch(this.action, {
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let list = document.querySelector("#shopping-list");
                let newItem = document.createElement("li");
                newItem.className = "list-group-item d-flex justify-content-between align-items-center";
                newItem.innerHTML = `${data.item.name} (${data.item.quantity}個)
                    <span class="badge bg-info">${data.item.category ? data.item.category.name : ''}</span>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${data.item.id}">削除</button>`;
                list.appendChild(newItem);
            }
        });
    });
});
