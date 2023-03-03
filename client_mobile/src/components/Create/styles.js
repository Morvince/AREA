import { StyleSheet } from 'react-native';
import { black, darkGray, lightGray, white } from "../../color";

export default styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    paddingTop: '15%'
  },
  servicesBar: {
    position: 'absolute',
    bottom: -80,
    borderTopWidth: 2,
    borderColor: lightGray,
    backgroundColor: white,
    marginRight: 5
  },
  actionsBar: {
    position: 'absolute',
    borderTopWidth: 1,
    borderColor: lightGray
  },
  actionBlocks: {
    width: 250,
    height: 110,
    marginLeft: 5,
    marginRight: 5,
    marginTop: 1,
    marginBottom: 1,
    borderRadius: 25,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 10
  },
  elevation: {
    elevation: 5,
    shadowColor: black
  },
  textActionBlocks: {
    color: white,
    fontSize: 15,
    fontWeight: 'bold',
    textAlign: 'center'
  },
  arrowActionBlocks: {
    position: 'absolute',
    bottom: 2,
    right: 3
  },
  sideBar: {
    position: 'absolute',
    right: 0,
    top: '5%',
    width: '0%',
    height: '200%',
    borderTopLeftRadius: 50,
    backgroundColor: white,
    zIndex: 100
  },
  inputText: {
    height: 80,
    width: 200,
    borderWidth: 1,
    borderColor: darkGray,
    borderRadius: 10,
    fontSize: 16,
    paddingHorizontal: 10,
    textAlign: 'center'
  },
});