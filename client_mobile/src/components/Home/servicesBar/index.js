import { Image, ScrollView, TouchableOpacity } from 'react-native';
import { Fontisto } from '@expo/vector-icons';
import styles from '../styles';

export default function ServicesBar({slideServicesBar, handleSlideActionsBar}) {
  return (
    <ScrollView contentContainerStyle={{width: '120%', height: 75, alignItems: 'center', justifyContent: 'space-between'}} horizontal={true}
    style={[styles.servicesBar, slideServicesBar && {bottom: 0}]}>
      <TouchableOpacity onPress={() => handleSlideActionsBar("discord")}>
        <Fontisto name="discord" size={65} color="#5765f2"/>
      </TouchableOpacity>
      <TouchableOpacity onPress={() => handleSlideActionsBar("spotify")}>
        <Fontisto name="spotify" size={65} color="#17d860"/>
      </TouchableOpacity>
      <TouchableOpacity onPress={() => handleSlideActionsBar("instagram")}>
        <Image
          source={require('../../../../assets/images/instagramIcon.png')}
          fadeDuration={0}
          style={styles.servicesIcons}
        />
      </TouchableOpacity>
      <TouchableOpacity onPress={() => handleSlideActionsBar("google")}>
        <Image
          source={require('../../../../assets/images/googleIcon.png')}
          fadeDuration={0}
          style={styles.servicesIcons}
        />
      </TouchableOpacity>
      <TouchableOpacity onPress={() => handleSlideActionsBar("twitter")}>
        <Fontisto name="twitter" size={60} color="#179cf0"/>
      </TouchableOpacity>
      <TouchableOpacity onPress={() => handleSlideActionsBar("github")}>
        <Fontisto name="github" size={65} color="black"/>
      </TouchableOpacity>
    </ScrollView>
  )
}