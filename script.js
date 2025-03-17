document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const sidebarToggle = document.getElementById("sidebarToggle");

    // Sidebar Toggle Function
    sidebarToggle.addEventListener("click", function () {
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("expanded");
    });
});

$(document).ready(function () {
    // Function to handle autocomplete for any input field
    function autocomplete(inputId, suggestionsId, searchUrl) {
        $(`#${inputId}`).on("input", function () {
            let query = $(this).val();
            if (query.length > 1) {
                $.ajax({
                    url: searchUrl,
                    type: "GET",
                    data: { query: query },
                    success: function (data) {
                        let suggestions = JSON.parse(data);
                        let suggestionsList = $(`#${suggestionsId}`);
                        suggestionsList.empty();

                        if (suggestions.length > 0) {
                            suggestions.forEach(function (item) {
                                suggestionsList.append(`<div class="suggestion">${item}</div>`);
                            });
                            suggestionsList.show();
                        } else {
                            suggestionsList.hide();
                        }
                    }
                });
            } else {
                $(`#${suggestionsId}`).hide();
            }
        });

        // Click to select suggestion
        $(document).on("click", `#${suggestionsId} .suggestion`, function () {
            $(`#${inputId}`).val($(this).text());
            $(`#${suggestionsId}`).hide();
        });

        // Hide suggestions when clicking outside
        $(document).click(function (e) {
            if (!$(e.target).closest(`#${suggestionsId}, #${inputId}`).length) {
                $(`#${suggestionsId}`).hide();
            }
        });
    }

    // Initialize autocomplete for both fields
    autocomplete("taken_by", "nameSuggestions", "search_names.php");
    autocomplete("company", "companySuggestions", "search_companies.php");
});