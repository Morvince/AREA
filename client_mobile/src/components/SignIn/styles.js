import { StyleSheet } from 'react-native';
import { black, lightPurple, white } from "../../color";

export default styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'space-evenly',
    alignItems: 'center',
  },
  inputFields: {
    height: 60,
    width: '80%',
    borderBottomWidth: 2,
    borderColor: lightPurple,
    borderRadius: 2,
    fontSize: 20,
    paddingHorizontal: 10,
    color: white,
    marginVertical: 25
  },
  LogInButton: {
    width: '42%',
    height: 72,
    backgroundColor: lightPurple,
    borderColor: lightPurple,
    borderRadius: 72,
    justifyContent: 'center',
    alignItems: 'center'
  },
  ErrorMessage: {
    fontSize: 18,
    fontStyle: 'italic',
    fontWeight: 'bold',
    color: black,
    bottom: 70
  }
});