<template>
    <div v-if="isVisible" class="modal" @keydown.esc="closeModal">
        <div class="modal-content modal-font ">
            <select v-model="assignedUser">
                <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                </option>
                <option v-if="!assignedUser" value="" disabled>wählen</option>
            </select>
            <p>Gewähltes Datum: {{ todayDate }}</p>
            <input v-model="title" placeholder="Name eingeben" ref="nameInput"/>
            <input v-model="time" type="time" value="12:12" @keyup.enter="saveAppointment" />
            <div class="button-container">
                <button @click="saveAppointment" class="styled-button">Save</button>
                <button @click="closeModal" class="styled-button">Close</button>
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
            todayDate: '',
            title: '',
            time: '',
            assignedUser: '',
            users: []
        };
    },
    mounted() {
        fetch('/users', {
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin' // wichtig, damit Cookies (Session/CSRF) mitgesendet werden
        })
            .then(response => response.json())
            .then(data => {
                this.users = Array.isArray(data) ? data : [];
                const loggedInUserId = document.querySelector('meta[name="logged-in-user-id"]')?.getAttribute('content');
                this.assignedUser = loggedInUserId || '';
            })
            .catch(error => {
                console.error('Fehler beim Laden der Benutzer:', error);
            });

    },
    methods: {
        openModal(info) {
            this.isVisible = true;
            this.time = "12:00";
            const [year, month, day] = info.startStr.split('T')[0].split('-');
            this.todayDate = `${day}.${month}.${year}`; // z. B. "01.05.2025"

            this.appointmentDate = info.startStr.split('T')[0]; // nur das Datum, z. B. "2025-05-01"
            this.$nextTick(() => {
                this.$refs.nameInput.focus();
            })
        },
        closeModal() {
            this.isVisible = false;
            this.title = '';
            this.time = '';
            const loggedInUser = document.querySelector('meta[name="logged-in-user-id"]')?.getAttribute('content');
            this.assignedUser = loggedInUser || '';
        },
        saveAppointment() {
            if (!this.title || !this.time || !this.assignedUser) {
                alert('Bitte alle Felder ausfüllen!');
                return;
            }

            const date = this.appointmentDate && this.appointmentDate.trim() !== ''
                ? this.appointmentDate
                : new Date().toISOString().split('T')[0]; // Fallback nur wenn leer
            const time = this.time;
            const startDateTime = `${date}T${time}:00`;

            fetch('/appointments/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    title: this.title,
                    start: startDateTime,
                    end: startDateTime,
                    assigned_to: parseInt(this.assignedUser)
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
                    this.users = Array.isArray(data) ? data : [];
                    window.location.reload();
                })
                .catch(error => {
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

p {
    text-align: center;
    padding: 10px;
}

    /* Kleine Bildschirme (Handys) */
    @media (max-width: 480px) {
        .modal {
            align-items: flex-end;
            padding-bottom: 20px;
        }
        .modal-content {
            max-width: 100%;  /* Vollbildbreite */
            max-height: 90vh;     /* max 90% der Höhe */
            overflow-y: auto; /* Scrollen bei zu viel Inhalt */
            padding: 15px;
            font-size: 16px;  /* evtl. etwas kleinere Schrift */
            border-radius: 0; /* evtl. ohne Rundungen */
        }

        p {
            text-align: center;
            padding: 10px;
            font-size: 10px;
        }

        .styled-button {
            width: 100%; /* Buttons werden breit, an Touch angepasst */
            height: 40px;
            font-size: 16px;
        }

        input, select {
            font-size: 16px;
            padding: 12px;
        }

        .button-container {
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .modal-font {
            font-size: 19px;
        }
    }

.modal-content {
    background: white;
    padding: 10px;
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
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    font-size: 16px;
}

select {
    width: 100%; /* Damit das Dropdown die volle Breite des Containers einnimmt */
    padding: 10px; /* Füge etwas Innenabstand hinzu */
    margin-bottom: 10px; /* Abstände unter dem Dropdown */
    font-size: 16px; /* Größere Schriftgröße */
}
</style>
