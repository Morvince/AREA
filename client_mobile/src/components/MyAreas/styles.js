import { StyleSheet } from 'react-native';
import { black, darkPurple, lightPurple, white } from "../../color";

export default styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    paddingTop: '15%'
  },
  containerAreas: {
    flex: 1,
    width: '98%',
    justifyContent: 'center',
    backgroundColor: white,
    borderWidth: 0.8,
    borderRadius: 32,
    borderColor: lightPurple,
    marginBottom: 8,
    marginTop: 18,
    elevation: 6,
    shadowColor: darkPurple
  },
  containerIconServices: {
    flexDirection: 'row',
    position: 'absolute',
    top: 0,
    left: 0,
    marginLeft: 15,
    marginTop: 5
  },
  menuBar: {
    position: 'absolute',
    left: 0,
    top: '5%',
    width: '0%',
    height: '200%',
    borderTopRightRadius: 50,
    backgroundColor: darkPurple,
    zIndex: 100,
    elevation: 5,
    shadowColor: black
  },
  logOutButton: {
    borderWidth: 0.4,
    borderColor: white,
    borderRadius: 10,
    backgroundColor: lightPurple,
    width: '70%',
    height: 55,
    justifyContent: 'center',
    elevation: 6,
    shadowColor: white,
    marginVertical: 22
  },
  textInputServerIp: {
    borderWidth: 0.4,
    borderColor: lightPurple,
    borderRadius: 10,
    backgroundColor: white,
    width: '85%',
    height: 55,
    justifyContent: 'center',
    elevation: 6,
    shadowColor: white,
    marginBottom: 20,
    marginTop: 9,
    color: black,
    fontSize: 16,
    textAlign: 'center',
    padding: 8
  }
});