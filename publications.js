
var app = angular.module('Arapp', []);


app.controller('PublicationsController', function($scope, $http) {
  $http.get("./publications.php?cmd=getpublications")
  .then(function(response) {
    $scope.years = response.data.years;
    $scope.publications = response.data.publications;
  });
});