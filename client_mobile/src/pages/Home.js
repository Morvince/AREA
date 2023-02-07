import { StyleSheet, Text, View, Button } from 'react-native';
import { Fontisto } from '@expo/vector-icons';

export default function Home({ navigation }) {
  return (
    <View style={styles.container}>
      <Text>Home Screen</Text>
      <Fontisto name="discord" size={70} color="#7289da" />
      <Fontisto name="spotify" size={70} color="#17d860" />
      <Fontisto name="twitter" size={70} color="#179cf0" />
      {/* <Button
        title="Go to Details"
        onPress={() => navigation.navigate('Details')}
      /> */}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
});