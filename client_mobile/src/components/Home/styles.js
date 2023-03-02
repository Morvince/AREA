import { StyleSheet } from 'react-native';
import { black, lightGray, white } from "../../color";

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
  servicesIcons: {
    width: 65,
    height: 65
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
    borderRadius: 25
  },
  elevation: {
    elevation: 5,
    shadowColor: black
  },
});