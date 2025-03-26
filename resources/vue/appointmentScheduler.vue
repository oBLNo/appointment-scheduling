<template>
    <div v-if="isVisible" class="modal">
        <div class="modal-content">
            <h1>Termin erstellen</h1>
            <p>Gewähltes Datum: {{ appointmentDate }}</p>
            <input v-model="title" placeholder="Terminname eingeben">
            <input v-model="hour" type="number" placeholder="Stunde (HH)">
            <input v-model="minute" type="number" placeholder="Minute (MM)">
            <button @click="saveAppointment">Speichern</button>
            <button @click="closeModal">Schließen</button>
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
            minute: ''
        };
    },
    methods: {
        openModal(date) {
            this.appointmentDate = date;
            this.isVisible = true;
        },
        closeModal() {
            this.isVisible = false;
        },
        saveAppointment() {
            if (!this.title || !this.hour || !this.minute) {
                alert('Bitte alle Felder ausfüllen!');
                return;
            }

            let startDateTime = this.appointmentDate + 'T' + this.hour + ':' + this.minute;

            fetch('/appointments/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: this.title,
                    start: startDateTime,
                    end: startDateTime
                })
            }).then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            }).then(data => {
                this.isVisible = false;
                window.location.reload(); // Kalender aktualisieren
            }).catch(error => {
                console.error('Fehler beim Speichern:', error);
                alert('Speichern fehlgeschlagen: ' + (error.message || 'Unbekannter Fehler'));
            });
        }
    }
}
</script>

<style>
.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; }
.modal-content { background: white; padding: 20px; border-radius: 10px; }
</style>
