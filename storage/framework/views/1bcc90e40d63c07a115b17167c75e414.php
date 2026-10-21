<?php $__env->startPush('scripts'); ?>
<script>
(function ($) {
    "use strict";

    window.initAirportMap = function () {
        $(function () {
            const form = $("#airportForm");
            const pointsInput = $("#place_points");
            const searchInput = document.getElementById("search-box");

            if ($.fn.validate) {
                form.validate({
                    ignore: [],
                    rules: {
                        name: "required",
                        place_points: "required"
                    }
                });
            }

            const map = new google.maps.Map(
                document.getElementById("map-container"),
                {
                    center: { lat: 18.58, lng: 73.92 },
                    zoom: 14,
                    mapTypeId: "roadmap"
                }
            );

            const polygon = new google.maps.Polygon({
                map: map,
                paths: [],
                editable: true,
                strokeColor: "#159B83",
                strokeWeight: 2,
                fillColor: "#159B83",
                fillOpacity: 0.25
            });

           const path = new google.maps.MVCArray();
polygon.setPath(path);
            let drawing = true;

            function updatePoints() {
                const coordinates = path.getArray().map(function (point) {
                    return { lat: point.lat(), lng: point.lng() };
                });

                if (coordinates.length < 3) {
                    pointsInput.val("").trigger("change");
                    return;
                }

                coordinates.push({ ...coordinates[0] });
                pointsInput
                    .val(JSON.stringify(coordinates))
                    .trigger("change");
            }

            path.addListener("insert_at", updatePoints);
            path.addListener("set_at", updatePoints);
            path.addListener("remove_at", updatePoints);

            map.addListener("click", function (event) {
                if (drawing && event.latLng) {
                    path.push(event.latLng);
                }
            });

            polygon.addListener("rightclick", function (event) {
                if (typeof event.vertex === "number") {
                    path.removeAt(event.vertex);
                }
            });

            const controls = document.createElement("div");
            controls.style.cssText =
                "background:white;padding:8px;margin:10px;" +
                "border-radius:4px;display:flex;gap:6px;";

            function addButton(label, action) {
                const button = document.createElement("button");
                button.type = "button";
                button.textContent = label;
                button.style.cssText =
                    "padding:6px 10px;cursor:pointer;" +
                    "background:white;border:1px solid #159B83;" +
                    "border-radius:4px;color:#126b5b;";
                button.addEventListener("click", action);
                controls.appendChild(button);
                return button;
            }

            const drawButton = addButton("Finish boundary", function () {
                if (drawing && path.getLength() < 3) {
                    alert("Boundary ke liye kam se kam 3 points lagao.");
                    return;
                }
                drawing = !drawing;
                drawButton.textContent = drawing
                    ? "Finish boundary"
                    : "Add points";
                updatePoints();
            });

            addButton("Undo", function () {
                if (path.getLength()) {
                    path.pop();
                }
            });

            addButton("Clear", function () {
                path.clear();
                drawing = true;
                drawButton.textContent = "Finish boundary";
                updatePoints();
            });

            map.controls[
                google.maps.ControlPosition.TOP_CENTER
            ].push(controls);

            let existing = <?php echo json_encode(isset($airport->locations) ? $airport->locations : null, 15, 512) ?>;

            try {
                if (typeof existing === "string") {
                    existing = JSON.parse(existing);
                }

                if (Array.isArray(existing)) {
                    const coordinates = existing.map(function (point) {
                        return {
                            lat: Number(point.lat),
                            lng: Number(point.lng)
                        };
                    });

                    const valid = coordinates.length >= 3 &&
                        coordinates.every(function (point) {
                            return Number.isFinite(point.lat) &&
                                Number.isFinite(point.lng) &&
                                Math.abs(point.lat) <= 90 &&
                                Math.abs(point.lng) <= 180;
                        });

                    if (valid) {
                        const first = coordinates[0];
                        const last = coordinates[coordinates.length - 1];

                        if (first.lat === last.lat && first.lng === last.lng) {
                            coordinates.pop();
                        }

                        if (coordinates.length >= 3) {
                            const bounds = new google.maps.LatLngBounds();

                            coordinates.forEach(function (point) {
                                path.push(point);
                                bounds.extend(point);
                            });

                            map.fitBounds(bounds);
                            drawing = false;
                            drawButton.textContent = "Add points";
                            updatePoints();
                        }
                    }
                }
            } catch (error) {
                console.error("Existing airport boundary invalid:", error);
            }

            const geocoder = new google.maps.Geocoder();

            function searchLocation() {
                const address = searchInput
                    ? searchInput.value.trim()
                    : "";

                if (!address) return;

                geocoder.geocode({ address: address }, function (results, status) {
                    if (status !== "OK" || !results || !results.length) {
                        alert("Location search failed: " + status);
                        return;
                    }

                    const geometry = results[0].geometry;

                    if (geometry.viewport) {
                        map.fitBounds(geometry.viewport);
                    } else {
                        map.setCenter(geometry.location);
                        map.setZoom(16);
                    }
                });
            }

            if (searchInput) {
                const searchButton = document.createElement("button");
                searchButton.type = "button";
                searchButton.textContent = "Find location";
                searchButton.className = "btn btn-outline-primary mt-2";
                searchInput.insertAdjacentElement("afterend", searchButton);
                searchButton.addEventListener("click", searchLocation);

                searchInput.addEventListener("keydown", function (event) {
                    if (event.key === "Enter") {
                        event.preventDefault();
                        searchLocation();
                    }
                });
            }

            form.on("submit.airportBoundary", function (event) {
                updatePoints();

                if (path.getLength() < 3) {
                    event.preventDefault();
                    alert("Map par kam se kam 3 boundary points select karo.");
                }
            });
        });
    };
})(jQuery);
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('GOOGLE_MAP_API_KEY')); ?>&callback=initAirportMap"
    async defer>
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/airport/google.blade.php ENDPATH**/ ?>