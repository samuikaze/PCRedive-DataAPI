document.addEventListener('DOMContentLoaded', function (e) {
    new Vue({
        el: '#header',
        data: {
            user: {},
            loading: true,
            routes: [
                {
                    name: '首頁',
                    route: '/admin'
                }
            ],
        },
        methods: {
            fireLogout: function () {
                Cookies.remove('token');
                window.location.href = '/admin/logout';
            }
        },
        created: function () {
            let token = Cookies.get('token');
            if (token != null) {
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }

            axios.get('/api/v1/user')
                .then((res) => {
                    this.user = res.data.data;
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        computed: {
            route: function () {
                return window.location.pathname;
            },
            routeClass: function () {
                return (r) => {
                    if (this.route == r.route) {
                        return (r.disabled === true) ? 'nav-item disabled' : 'nav-item active';
                    } else {
                        return (r.disabled === true) ? 'nav-item disabled' : 'nav-item';
                    }
                }
            }
        }
    });
});
