import {defineStore} from "pinia";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        token: null,
        error: null,
    }),
    getters: {
        isLoggedIn: (state) => state.user !== null,
        getUser: (state) => state.user,
    },
    actions: {
        setUser(user) {
            // Save the user to the local storage
            localStorage.setItem("user", JSON.stringify(user));

            // Set the user
            this.user = user;
        },
        setToken(token) {
            // Save the token to the local storage
            localStorage.setItem("token", token);

            // Set the token
            this.token = token;
        },
        async fetchUser() {
            // Call the user API
            let response = await axios.get("/api/me")
            if (response.status !== 200) {
                throw new Error(response.data);
            }

            // Set the user
            this.setUser(response.data.user);
        },
        async login(credentials) {
            // Call the login API
            try {
                let response = await axios.post("/api/auth/login", credentials)
                // Set the user and token
                this.setUser(response.data.data.user);
                this.setToken(response.data.data.token);

                return true;
            } catch (error) {
                // Set the error
                this.error = error.response.data.message;
                return false;
            }
        },
        logout() {
            // Remove the user and token from the local storage
            localStorage.removeItem("user");
            localStorage.removeItem("token");

            // Reset the user and token
            this.user = null;
            this.token = null;
        },
        async checkAuth() {
            // check local storage for user and token
            let user = localStorage.getItem("user");
            let token = localStorage.getItem("token");

            // if no user or token, return false
            if (!user || !token) {
                return false;
            }
            try {
                // test the token
                const response = await axios.get("/api/me", {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                // set the user and token
                this.user = response.data.data;
                this.token = token;

                return true
            } catch (error) {
                // remove the user and token from local storage
                localStorage.removeItem("user");
                localStorage.removeItem("token");
                this.user = null;
                this.token = null;

                return false;
            }
        },
        setError(error) {
            this.error = error;
        },
        async getRoles() {
            if (!this.user) {
                return [];
            }
            return this.user.roles.map(role => role.name);
        }
    },
});
