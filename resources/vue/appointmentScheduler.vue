<template>
    <div v-if="isVisible" class="modal">
        <div class="modal-content modal-font ">
            <select v-model="assignedUser">
                <option value="" disabled>Bitte wählen</option>
                <option v-for="user in users" :key="user.id" :value="user.name">
                    {{ user.name }}
                </option>
            </select>
            <p style="text-align: center; padding: 10px;">Gewähltes Datum: {{ appointmentDate }}</p>
            <input v-model="title" placeholder="Name eingeben" ref="nameInput"/>
            <input v-model="hour" type="text" placeholder="Stunde (HH)" maxlength="2"/>
            <input v-model="minute" type="text" placeholder="Minute (MM)" maxlength="2" @keyup.enter="saveAppointment"/>
            <div class="button-container">
                <button @click="saveAppointment" class="styled-button">Speichern</button>
                <button @click="closeModal" class="styled-button">Schließen</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isVisible: false,
            appointmentDate: '',
            title: '',
            hour: '',
            minute: '',
            assignedUser: '',
            users: []
        };
    },
    mounted() {
        console.log('Vue-Komponente geladen:', this.$el);
        fetch('/users', {
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin' // wichtig, damit Cookies (Session/CSRF) mitgesendet werden
        })
            .then(response => response.json())
            .then(data => {
                console.log('Benutzer-Daten:', data); // Hier wird die Antwort angezeigt
                console.log('Antwortstruktur der Benutzer-API:', data);
                this.users = Array.isArray(data) ? data : [];
            })
            .catch(error => {
                console.error('Fehler beim Laden der Benutzer:', error);
            });
    },
    methods: {
        openModal(date) {
            console.log("openModal wurde aufgerufen!");
            this.isVisible = true;
            this.appointmentDate = date;

            this.$nextTick(() => {
                this.$refs.nameInput.focus();3
            })
        },
        closeModal() {
            this.isVisible = false;
        },
        saveAppointment() {
            if (!this.title || !this.hour || !this.minute || !this.assignedUser) {
                alert('Bitte alle Felder ausfüllen!');
                return;
            }

            let startDateTime = this.appointmentDate + 'T' + this.hour + ':' + this.minute;

            fetch('/appointments/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: this.title,
                    title: this.title,
                    start: startDateTime,
                    end: startDateTime,
                    assigned_user: this.assignedUser
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.isVisible = false;
                    window.location.reload(); // Kalender aktualisieren
                })
                .catch(error => {
                    console.error('Fehler beim Speichern:', error);
                    alert('Speichern fehlgeschlagen: ' + (error.message || 'Unbekannter Fehler'));
                });
        }
    }
}
</script>

<style>

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
}

.modal-font {
    font-size: 19px;
}

.button-container {
    display: flex;
    margin-top: 20px;
    justify-content: space-around;
    gap: 10px;
}

.styled-button {
    border: 1px solid black;
    width: 220px;
    height: 30px;
    background-color: white;
    text-align: center;
    cursor: pointer;
}

input {
    border: 1px black solid !important;
}

select {
    width: 100%; /* Damit das Dropdown die volle Breite des Containers einnimmt */
    padding: 10px; /* Füge etwas Innenabstand hinzu */
    margin-bottom: 10px; /* Abstände unter dem Dropdown */
    font-size: 16px; /* Größere Schriftgröße */
}

</style>
