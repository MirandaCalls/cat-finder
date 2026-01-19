<x-app-layout>
    <div class="h-[calc(100vh-64px)]" x-data="mapApp()" x-init="initMap()">
        <!-- Map Container -->
        <div id="map" class="w-full h-full"></div>

        <!-- Add Pin Modal -->
        <div x-show="showAddModal"
             x-cloak
             class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center"
             @keydown.escape.window="showAddModal = false">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="showAddModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-white w-full sm:w-96 sm:rounded-lg rounded-t-2xl shadow-xl max-h-[80vh] overflow-auto">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Add Location</h3>
                    <p class="text-sm text-gray-500" x-text="'Lat: ' + newPin.latitude.toFixed(6) + ', Lng: ' + newPin.longitude.toFixed(6)"></p>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Pin Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What is this location?</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    @click="newPin.type = 'house'"
                                    :class="newPin.type === 'house' ? 'ring-2 ring-blue-500 bg-blue-50' : 'bg-gray-50 hover:bg-gray-100'"
                                    class="p-3 rounded-lg border text-center transition-all">
                                <div class="text-2xl mb-1">🏠</div>
                                <div class="text-sm font-medium">House</div>
                            </button>
                            <button type="button"
                                    @click="newPin.type = 'flyer'"
                                    :class="newPin.type === 'flyer' ? 'ring-2 ring-green-500 bg-green-50' : 'bg-gray-50 hover:bg-gray-100'"
                                    class="p-3 rounded-lg border text-center transition-all">
                                <div class="text-2xl mb-1">📄</div>
                                <div class="text-sm font-medium">Flyer Location</div>
                            </button>
                        </div>
                    </div>

                    <!-- House Options -->
                    <div x-show="newPin.type === 'house'" class="space-y-3">
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" x-model="newPin.flyer_left" class="w-5 h-5 rounded text-blue-600">
                            <span class="text-sm font-medium">Left a flyer</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" x-model="newPin.talked_to_owners" class="w-5 h-5 rounded text-blue-600">
                            <span class="text-sm font-medium">Talked to owners</span>
                        </label>
                    </div>

                    <!-- Address Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address / Description (optional)</label>
                        <input type="text"
                               x-model="newPin.address"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., 123 Main St">
                    </div>

                    <!-- Notes Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                        <textarea x-model="newPin.notes"
                                  rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>

                <div class="p-4 border-t flex gap-2">
                    <button type="button"
                            @click="showAddModal = false"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="button"
                            @click="savePin()"
                            :disabled="saving"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Pin Modal -->
        <div x-show="showEditModal"
             x-cloak
             class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center"
             @keydown.escape.window="showEditModal = false">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="showEditModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-white w-full sm:w-96 sm:rounded-lg rounded-t-2xl shadow-xl max-h-[80vh] overflow-auto">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="editPin.type === 'house' ? 'Edit House' : 'Edit Flyer Location'"></h3>
                    <p class="text-sm text-gray-500" x-text="editPin.address || 'No address'"></p>
                </div>

                <div class="p-4 space-y-4">
                    <!-- House Options -->
                    <div x-show="editPin.type === 'house'" class="space-y-3">
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" x-model="editPin.flyer_left" class="w-5 h-5 rounded text-blue-600">
                            <span class="text-sm font-medium">Left a flyer</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" x-model="editPin.talked_to_owners" class="w-5 h-5 rounded text-blue-600">
                            <span class="text-sm font-medium">Talked to owners</span>
                        </label>
                    </div>

                    <!-- Address Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address / Description</label>
                        <input type="text"
                               x-model="editPin.address"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., 123 Main St">
                    </div>

                    <!-- Notes Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea x-model="editPin.notes"
                                  rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>

                <div class="p-4 border-t flex gap-2">
                    <button type="button"
                            @click="deletePin()"
                            :disabled="saving"
                            class="px-4 py-2 text-red-700 bg-red-100 rounded-lg hover:bg-red-200 font-medium disabled:opacity-50">
                        Delete
                    </button>
                    <button type="button"
                            @click="showEditModal = false"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="button"
                            @click="updatePin()"
                            :disabled="saving"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="fixed bottom-4 left-4 z-[999] bg-white rounded-lg shadow-lg p-3 text-sm">
            <div class="font-medium mb-2">Legend</div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-red-500"></span>
                    <span>House - Not visited</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-yellow-500"></span>
                    <span>House - Partial</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-green-500"></span>
                    <span>House - Complete</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-purple-500"></span>
                    <span>Flyer Location</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>You are here</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-orange-500"></span>
                    <span>Other users</span>
                </div>
            </div>
            <!-- Active Users Count -->
            <div x-show="activeUsers.length > 0" class="mt-2 pt-2 border-t border-gray-200 text-xs text-gray-600">
                <span x-text="activeUsers.length"></span> other user<span x-show="activeUsers.length !== 1">s</span> active
            </div>
            <!-- Live Sync Status -->
            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-2">
                <span x-show="syncStatus === 'synced'" class="w-2 h-2 rounded-full bg-green-500"></span>
                <span x-show="syncStatus === 'syncing'" class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                <span x-show="syncStatus === 'error'" class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-500">
                    <span x-show="syncStatus === 'synced'">Live updates active</span>
                    <span x-show="syncStatus === 'syncing'">Syncing...</span>
                    <span x-show="syncStatus === 'error'">Connection error</span>
                </span>
            </div>
        </div>

        <!-- Location Button -->
        <button @click="centerOnUser()"
                class="fixed bottom-4 right-4 z-[999] bg-white rounded-full shadow-lg p-3 hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>

    @push('scripts')
    <script>
        function mapApp() {
            return {
                map: null,
                userMarker: null,
                userLocation: null,
                houses: [],
                flyerLocations: [],
                markers: [],
                activeUsers: [],
                userMarkers: {},
                locationSharingEnabled: false,
                showAddModal: false,
                showEditModal: false,
                saving: false,
                pollInterval: null,
                locationInterval: null,
                lastSync: null,
                syncStatus: 'syncing',
                newPin: {
                    type: 'house',
                    latitude: 0,
                    longitude: 0,
                    address: '',
                    notes: '',
                    flyer_left: false,
                    talked_to_owners: false,
                },
                editPin: {
                    id: null,
                    type: 'house',
                    latitude: 0,
                    longitude: 0,
                    address: '',
                    notes: '',
                    flyer_left: false,
                    talked_to_owners: false,
                },

                initMap() {
                    // Initialize map with a default center (will update when we get user location)
                    this.map = L.map('map').setView([37.7749, -122.4194], 15);

                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(this.map);

                    // Handle map click to add new pin
                    this.map.on('click', (e) => {
                        this.newPin = {
                            type: 'house',
                            latitude: e.latlng.lat,
                            longitude: e.latlng.lng,
                            address: '',
                            notes: '',
                            flyer_left: false,
                            talked_to_owners: false,
                        };
                        this.showAddModal = true;
                    });

                    // Get user location
                    this.getUserLocation();

                    // Load existing pins and start live updates
                    this.loadPins();
                    this.startPolling();

                    // Clean up polling when page is hidden/closed
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.stopPolling();
                            this.stopLocationSharing();
                        } else {
                            this.loadPins();
                            this.loadActiveUsers();
                            this.startPolling();
                            if (this.userLocation) {
                                this.startLocationSharing();
                            }
                        }
                    });

                    // Clear location when leaving/closing the page
                    window.addEventListener('beforeunload', () => {
                        this.clearLocationOnServer();
                    });

                    window.addEventListener('pagehide', () => {
                        this.clearLocationOnServer();
                    });
                },

                startPolling() {
                    // Poll every 5 seconds for updates
                    if (this.pollInterval) return;
                    this.pollInterval = setInterval(() => {
                        this.loadPins(true);
                        this.loadActiveUsers();
                    }, 5000);
                },

                stopPolling() {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                        this.pollInterval = null;
                    }
                },

                startLocationSharing() {
                    if (this.locationInterval || !this.userLocation) return;
                    this.locationSharingEnabled = true;
                    // Share location immediately
                    this.shareLocationToServer();
                    // Then every 5 seconds
                    this.locationInterval = setInterval(() => {
                        this.shareLocationToServer();
                    }, 5000);
                },

                stopLocationSharing() {
                    this.locationSharingEnabled = false;
                    if (this.locationInterval) {
                        clearInterval(this.locationInterval);
                        this.locationInterval = null;
                    }
                    this.clearLocationOnServer();
                },

                async shareLocationToServer() {
                    if (!this.userLocation) return;
                    try {
                        await fetch('/api/user-location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                latitude: this.userLocation.lat,
                                longitude: this.userLocation.lng,
                            }),
                        });
                    } catch (error) {
                        console.error('Error sharing location:', error);
                    }
                },

                clearLocationOnServer() {
                    // Use sendBeacon for reliability when page is closing
                    const data = new FormData();
                    data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    data.append('_method', 'DELETE');
                    navigator.sendBeacon('/api/user-location', data);
                },

                async loadActiveUsers() {
                    try {
                        const response = await fetch('/api/user-locations', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        const users = await response.json();
                        this.activeUsers = users;
                        this.renderUserMarkers();
                    } catch (error) {
                        console.error('Error loading active users:', error);
                    }
                },

                renderUserMarkers() {
                    // Get current user IDs on the map
                    const currentUserIds = new Set(Object.keys(this.userMarkers).map(id => parseInt(id)));
                    const newUserIds = new Set(this.activeUsers.map(u => u.id));

                    // Remove markers for users no longer active
                    currentUserIds.forEach(id => {
                        if (!newUserIds.has(id)) {
                            this.map.removeLayer(this.userMarkers[id]);
                            delete this.userMarkers[id];
                        }
                    });

                    // Add or update markers for active users
                    this.activeUsers.forEach(user => {
                        if (this.userMarkers[user.id]) {
                            // Update position
                            this.userMarkers[user.id].setLatLng([user.latitude, user.longitude]);
                        } else {
                            // Create new marker with a different color
                            const userIcon = L.divIcon({
                                className: 'other-user-marker',
                                html: `<div class="w-5 h-5 bg-orange-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">${user.name.charAt(0).toUpperCase()}</span>
                                </div>`,
                                iconSize: [20, 20],
                                iconAnchor: [10, 10],
                            });

                            this.userMarkers[user.id] = L.marker([user.latitude, user.longitude], { icon: userIcon })
                                .addTo(this.map)
                                .bindPopup(`<strong>${user.name}</strong><br><span class="text-gray-500 text-sm">Active now</span>`);
                        }
                    });
                },

                hasDataChanged(newHouses, newFlyers) {
                    // Quick check if data has changed to avoid unnecessary re-renders
                    if (newHouses.length !== this.houses.length || newFlyers.length !== this.flyerLocations.length) {
                        return true;
                    }

                    const housesChanged = newHouses.some((house, i) => {
                        const old = this.houses[i];
                        return !old ||
                            house.id !== old.id ||
                            house.flyer_left !== old.flyer_left ||
                            house.talked_to_owners !== old.talked_to_owners ||
                            house.address !== old.address ||
                            house.notes !== old.notes;
                    });

                    const flyersChanged = newFlyers.some((flyer, i) => {
                        const old = this.flyerLocations[i];
                        return !old ||
                            flyer.id !== old.id ||
                            flyer.address !== old.address ||
                            flyer.notes !== old.notes;
                    });

                    return housesChanged || flyersChanged;
                },

                getUserLocation() {
                    if ('geolocation' in navigator) {
                        navigator.geolocation.watchPosition(
                            (position) => {
                                const isFirstLocation = !this.userLocation;
                                this.userLocation = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };

                                // Update or create user marker
                                if (this.userMarker) {
                                    this.userMarker.setLatLng([this.userLocation.lat, this.userLocation.lng]);
                                } else {
                                    const userIcon = L.divIcon({
                                        className: 'user-location-marker',
                                        html: '<div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg animate-pulse"></div>',
                                        iconSize: [16, 16],
                                        iconAnchor: [8, 8],
                                    });

                                    this.userMarker = L.marker([this.userLocation.lat, this.userLocation.lng], { icon: userIcon })
                                        .addTo(this.map)
                                        .bindPopup('You are here');

                                    // Center map on user location on first load
                                    this.map.setView([this.userLocation.lat, this.userLocation.lng], 16);
                                }

                                // Start sharing location with other users
                                if (isFirstLocation) {
                                    this.startLocationSharing();
                                    this.loadActiveUsers();
                                }
                            },
                            (error) => {
                                console.error('Error getting location:', error);
                            },
                            {
                                enableHighAccuracy: true,
                                maximumAge: 10000,
                                timeout: 5000,
                            }
                        );
                    }
                },

                centerOnUser() {
                    if (this.userLocation) {
                        this.map.setView([this.userLocation.lat, this.userLocation.lng], 16);
                    } else {
                        alert('Location not available. Please enable location services.');
                    }
                },

                async loadPins(isPolling = false) {
                    try {
                        this.syncStatus = 'syncing';

                        const [housesRes, flyersRes] = await Promise.all([
                            fetch('/api/houses', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                credentials: 'same-origin',
                            }),
                            fetch('/api/flyer-locations', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                credentials: 'same-origin',
                            }),
                        ]);

                        const newHouses = await housesRes.json();
                        const newFlyers = await flyersRes.json();

                        // Only re-render if data has changed (avoids flickering during polling)
                        if (!isPolling || this.hasDataChanged(newHouses, newFlyers)) {
                            this.houses = newHouses;
                            this.flyerLocations = newFlyers;
                            this.renderPins();
                        }

                        this.lastSync = new Date();
                        this.syncStatus = 'synced';
                    } catch (error) {
                        console.error('Error loading pins:', error);
                        this.syncStatus = 'error';
                    }
                },

                renderPins() {
                    // Clear existing markers
                    this.markers.forEach(marker => this.map.removeLayer(marker));
                    this.markers = [];

                    // Add house markers
                    this.houses.forEach(house => {
                        const color = this.getHouseColor(house);
                        const icon = L.divIcon({
                            className: 'house-marker',
                            html: `<div class="w-6 h-6 ${color} rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs">🏠</div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12],
                        });

                        const marker = L.marker([house.latitude, house.longitude], { icon })
                            .addTo(this.map)
                            .on('click', (e) => {
                                L.DomEvent.stopPropagation(e);
                                this.openEditModal(house, 'house');
                            });

                        this.markers.push(marker);
                    });

                    // Add flyer location markers
                    this.flyerLocations.forEach(flyer => {
                        const icon = L.divIcon({
                            className: 'flyer-marker',
                            html: '<div class="w-6 h-6 bg-purple-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs">📄</div>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12],
                        });

                        const marker = L.marker([flyer.latitude, flyer.longitude], { icon })
                            .addTo(this.map)
                            .on('click', (e) => {
                                L.DomEvent.stopPropagation(e);
                                this.openEditModal(flyer, 'flyer');
                            });

                        this.markers.push(marker);
                    });
                },

                getHouseColor(house) {
                    if (house.flyer_left && house.talked_to_owners) {
                        return 'bg-green-500';
                    } else if (house.flyer_left || house.talked_to_owners) {
                        return 'bg-yellow-500';
                    }
                    return 'bg-red-500';
                },

                openEditModal(pin, type) {
                    this.editPin = {
                        id: pin.id,
                        type: type,
                        latitude: pin.latitude,
                        longitude: pin.longitude,
                        address: pin.address || '',
                        notes: pin.notes || '',
                        flyer_left: pin.flyer_left || false,
                        talked_to_owners: pin.talked_to_owners || false,
                    };
                    this.showEditModal = true;
                },

                async savePin() {
                    this.saving = true;
                    try {
                        const url = this.newPin.type === 'house' ? '/api/houses' : '/api/flyer-locations';
                        const data = {
                            latitude: this.newPin.latitude,
                            longitude: this.newPin.longitude,
                            address: this.newPin.address || null,
                            notes: this.newPin.notes || null,
                        };

                        if (this.newPin.type === 'house') {
                            data.flyer_left = this.newPin.flyer_left;
                            data.talked_to_owners = this.newPin.talked_to_owners;
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(data),
                        });

                        if (response.ok) {
                            this.showAddModal = false;
                            await this.loadPins();
                        } else {
                            const error = await response.json();
                            alert('Error saving pin: ' + (error.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error saving pin:', error);
                        alert('Error saving pin');
                    } finally {
                        this.saving = false;
                    }
                },

                async updatePin() {
                    this.saving = true;
                    try {
                        const url = this.editPin.type === 'house'
                            ? `/api/houses/${this.editPin.id}`
                            : `/api/flyer-locations/${this.editPin.id}`;

                        const data = {
                            address: this.editPin.address || null,
                            notes: this.editPin.notes || null,
                        };

                        if (this.editPin.type === 'house') {
                            data.flyer_left = this.editPin.flyer_left;
                            data.talked_to_owners = this.editPin.talked_to_owners;
                        }

                        const response = await fetch(url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(data),
                        });

                        if (response.ok) {
                            this.showEditModal = false;
                            await this.loadPins();
                        } else {
                            const error = await response.json();
                            alert('Error updating pin: ' + (error.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error updating pin:', error);
                        alert('Error updating pin');
                    } finally {
                        this.saving = false;
                    }
                },

                async deletePin() {
                    if (!confirm('Are you sure you want to delete this pin?')) {
                        return;
                    }

                    this.saving = true;
                    try {
                        const url = this.editPin.type === 'house'
                            ? `/api/houses/${this.editPin.id}`
                            : `/api/flyer-locations/${this.editPin.id}`;

                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });

                        if (response.ok) {
                            this.showEditModal = false;
                            await this.loadPins();
                        } else {
                            alert('Error deleting pin');
                        }
                    } catch (error) {
                        console.error('Error deleting pin:', error);
                        alert('Error deleting pin');
                    } finally {
                        this.saving = false;
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
