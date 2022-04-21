

export default () => {

  instance.interceptors.response.use((response) => {
      if(response.status === 401) {
           //add your code
           alert("You are not authorized");
      }
      return response;
  }, (error) => {
      if (error.response && error.response.data) {
           //add your code
           return Promise.reject(error.response.data);
      }
      return Promise.reject(error.message);
  });

  return instance;
}
