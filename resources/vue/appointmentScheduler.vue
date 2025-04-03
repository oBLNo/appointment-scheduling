<template>
    <div v-if="isVisible" class="modal">
        <div class="modal-content modal-font ">
            <p style="text-align: center; padding: 10px;">Gewähltes Datum: {{ appointmentDate }}</p>
            <input v-model="title" placeholder="Name eingeben"/>
            <input v-model="hour" type="number" placeholder="Stunde (HH)"/>
            <input v-model="minute" type="number" placeholder="Minute (MM)"/>
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
            minute: ''
        };
    },
    mounted() {
        console.log('Vue-Komponente geladen:', this.$el);
    },
    methods: {
        openModal(date) {
            const parsedDate = new Date(date);
            const formattedDate = parsedDate.toLocaleDateString('de-DE');
            this.appointmentDate = formattedDate;

            this.isVisible = true;
            console.log('Modal sichtbar:', this.isVisible);
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
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
}
.modal-font {
  font-size: 15px;
}

.button-container {
    display: flex;
    margin-top: 20px;
    justify-content: space-around;
    gap: 10px;
}

.styled-button {
    border: 2px solid black; /* Sichtbarer Rand */
    padding: 10px 30px; /* Innenabstand: Höhe (10px), Breite (30px) */
    width: 150px; /* Fixe Breite für längere Buttons */
    height: 40px; /* Höhe für schmalere Optik */
    background-color: white; /* Falls du eine Hintergrundfarbe möchtest */
    text-align: center; /* Text zentrieren */
    cursor: pointer;
}

</style>
