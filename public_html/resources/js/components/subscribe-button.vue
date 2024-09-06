<template>
    <div class="subscription-container">
        <button @click="toggleSubscription" class="subscribe-button" :style="{ backgroundColor: buttonColor }">
            {{ owner ? "" : subscribed ? "Unsubscribe" : "Subscribe" }}
            <span class="subscriber-count">{{ count }}</span>
            {{ owner ? "Subscribers" : "" }}
        </button>
        <button @click="toggleNotification" class="bell-button">
            <i :class="bellIcon"></i>
        </button>
    </div>
</template>

<script>
import numeral from "numeral";

export default {
    props: {
        channel: {
            type: Object,
            required: true,
            default: () => ({})
        },
        initialSubscriptions: {
            type: Array,
            required: true,
            default: () => []
        }
    },
    data: function () {
        return {
            subscriptions: this.initialSubscriptions,
            notificationsEnabled: false,
            defaultColor: '',
            hoverColor: '',
            buttonColor: '',
        };
    },
    computed: {
        subscribed() {
            if (!__auth() || this.channel.user_id === __auth().id) return false;
            return !!this.subscription;
        },
        owner() {
            return __auth() && this.channel.user_id === __auth().id;
        },
        count() {
            return numeral(this.subscriptions.length).format('0a');
        },
        subscription() {
            if (!__auth()) return null;
            return this.subscriptions.find(subscription => subscription.user_id === __auth().id);
        },
        bellIcon() {
            return this.notificationsEnabled ? 'fas fa-bell' : 'far fa-bell';
        }
    },
    mounted() {
        this.setThemeColors();
    },
    methods: {
        setThemeColors() {
            const button = document.querySelector('.subscribe-button');
            
            // Get default background color from the button
            const computedStyle = getComputedStyle(button);
            this.defaultColor = computedStyle.backgroundColor;

            // Create a temporary element, apply hover class, and measure hover background
            const tempElement = document.createElement('div');
            tempElement.style.position = 'absolute';
            tempElement.style.visibility = 'hidden';
            tempElement.classList.add('subscribe-button', 'temp-hover');
            document.body.appendChild(tempElement);

            // Get the hover background color
            const tempComputedStyle = getComputedStyle(tempElement);
            this.hoverColor = tempComputedStyle.backgroundColor;

            // Clean up the temporary element
            document.body.removeChild(tempElement);

            console.log("Default Color:", this.defaultColor, "Hover Color:", this.hoverColor);

            this.updateButtonColor();
        },
        updateButtonColor() {
            // Update button color based on subscription status
            this.buttonColor = this.subscribed ? this.hoverColor : this.defaultColor;
        },
        toggleSubscription() {
            if (!__auth()) {
                return alert('Please login to subscribe.');
            }
            if (this.owner) {
                return alert('You cannot subscribe to your own channel.');
            }
            if (this.subscribed) {
                axios.delete(`/channels/${this.channel.id}/subscriptions/${this.subscription.id}`)
                    .then(() => {
                        this.subscriptions = this.subscriptions.filter(s => s.id !== this.subscription.id);
                        this.updateButtonColor();
                    });
            } else {
                axios.post(`/channels/${this.channel.id}/subscriptions`)
                    .then(response => {
                        this.subscriptions = [...this.subscriptions, response.data];
                        this.updateButtonColor();
                    });
            }
        },
        toggleNotification() {
            this.notificationsEnabled = !this.notificationsEnabled;
            // Optionally, send request to server to update notification preferences
        }
    }
};
</script>
