require.config({
  baseUrl: '../wp-content/themes/json',
  paths: {
    jquery  : '../vendor/jquery/dist/jquery',
    angular : '../vendor/angular/angular',
    animate : '../vendor/angular-animate/angular-animate',
    router  : '../vendor/angular-ui-router/release/angular-ui-router'
  },
  shim: {
    jquery: {
      exports: '$'
    },
    angular: {
      exports: 'angular',
      deps: ['jquery']
    },
    animate: ['angular'],
    router: ['angular']
  },
  deps: ['./app/bootstrap']
});