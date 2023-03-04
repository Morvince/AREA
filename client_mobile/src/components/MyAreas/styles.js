import { StyleSheet } from 'react-native';
import { darkPurple, lightPurple, white } from "../../color";

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
    backgroundColor: white,
    borderWidth: 0.8,
    borderRadius: 32,
    borderColor: lightPurple,
    marginBottom: 8,
    marginTop: 18,
    elevation: 6,
    shadowColor: darkPurple
  }
});